<?php
namespace H4ck3r31\BankAccountExample\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use H4ck3r31\BankAccountExample\Domain\Model\Account\Command;
use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountHolder;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Money;
use H4ck3r31\BankAccountExample\Domain\Model\DtoConverter;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionDto;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionReference;
use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountDto;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\BankDto;
use TYPO3\CMS\DataHandling\Core\Framework\Process\CommandBus;

/**
 * CommandController
 */
class CommandController extends AbstractController
{
    /**
     * @param AccountDto $accountDto
     * @param BankDto $bankDto
     */
    public function createAction(AccountDto $accountDto, BankDto $bankDto)
    {
        $this->finalRedirect = ['listAccounts', 'Management', null, ['bankDto' => $bankDto->toArray()]];

        $command = Command\CreateAccountCommand::create(
            DtoConverter::fromBankDto($bankDto),
            AccountHolder::create($accountDto->getAccountHolder()),
            $accountDto->getAccountNumber()
        );
        CommandBus::provide()->handle($command);
    }

    /**
     * @param AccountDto $accountDto
     */
    public function updateAction(AccountDto $accountDto)
    {
        $iban = Iban::fromString($accountDto->getIban());
        $bankDto = DtoConverter::ibanToBankDto($iban);
        $this->finalRedirect = ['listAccounts', 'Management', null, ['bankDto' => $bankDto->toArray()]];

        $command = Command\ChangeAccountHolderCommand::create(
            $iban,
            AccountHolder::create($accountDto->getAccountHolder())
        );
        CommandBus::provide()->handle($command);
    }

    /**
     * @param Iban $iban
     * @param TransactionDto $transactionDto
     */
    public function depositAction(Iban $iban, TransactionDto $transactionDto)
    {
        $this->finalRedirect = ['show', 'Management', null, ['iban' => (string)$iban]];

        $command = Command\DepositMoneyCommand::create(
            $iban,
            Money::create($transactionDto->getMoney()),
            TransactionReference::create($transactionDto->getReference()),
            $transactionDto->getAvailabilityDate()
        );
        CommandBus::provide()->handle($command);
    }

    /**
     * @param Iban $iban
     * @param TransactionDto $transactionDto
     */
    public function debitAction(Iban $iban, TransactionDto $transactionDto)
    {
        $this->finalRedirect = ['show', 'Management', null, ['iban' => (string)$iban]];

        $command = Command\DebitMoneyCommand::create(
            $iban,
            Money::create($transactionDto->getMoney()),
            TransactionReference::create($transactionDto->getReference()),
            $transactionDto->getAvailabilityDate()
        );
        CommandBus::provide()->handle($command);
    }

    /**
     * @param Iban $iban
     */
    public function closeAction(Iban $iban)
    {
        $bankDto = DtoConverter::ibanToBankDto($iban);
        $this->finalRedirect = ['listAccounts', 'Management', null, ['bankDto' => $bankDto->toArray()]];

        $command = Command\CloseAccountCommand::create($iban);
        CommandBus::provide()->handle($command);
    }
}
