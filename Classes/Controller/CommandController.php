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
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
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
        try {
            $command = Command\CreateAccountCommand::create(
                DtoConverter::fromBankDto($bankDto),
                AccountHolder::create($accountDto->getAccountHolder()),
                $accountDto->getAccountNumber()
            );
            CommandBus::provide()->handle($command);
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('listAccounts', 'Management', null, ['bankDto' => $bankDto->toArray()]);
    }

    /**
     * @param AccountDto $accountDto
     */
    public function updateAction(AccountDto $accountDto)
    {
        $iban = Iban::fromString($accountDto->getIban());
        $bankDto = DtoConverter::ibanToBankDto($iban);

        try {
            $command = Command\ChangeAccountHolderCommand::create(
                $iban,
                AccountHolder::create($accountDto->getAccountHolder())
            );
            CommandBus::provide()->handle($command);
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('listAccounts', 'Management', null, ['bankDto' => $bankDto->toArray()]);
    }

    /**
     * @param Iban $iban
     * @param TransactionDto $transactionDto
     */
    public function depositAction(Iban $iban, TransactionDto $transactionDto)
    {
        try {
            $command = Command\DepositMoneyCommand::create(
                $iban,
                Money::create($transactionDto->getMoney()),
                TransactionReference::create($transactionDto->getReference()),
                $transactionDto->getAvailabilityDate()
            );
            CommandBus::provide()->handle($command);
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('show', 'Management', null, ['iban' => (string)$iban]);
    }

    /**
     * @param Iban $iban
     * @param TransactionDto $transactionDto
     */
    public function debitAction(Iban $iban, TransactionDto $transactionDto)
    {
        try {
            $command = Command\DebitMoneyCommand::create(
                $iban,
                Money::create($transactionDto->getMoney()),
                TransactionReference::create($transactionDto->getReference()),
                $transactionDto->getAvailabilityDate()
            );
            CommandBus::provide()->handle($command);
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('show', 'Management', null, ['iban' => (string)$iban]);
    }

    /**
     * @param Iban $iban
     */
    public function closeAction(Iban $iban)
    {
        try {
            $command = Command\CloseAccountCommand::create($iban);
            CommandBus::provide()->handle($command);
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $bankDto = DtoConverter::ibanToBankDto($iban);
        $this->redirect('listAccounts', 'Management', null, ['bankDto' => $bankDto->toArray()]);
    }
}
