<?php
namespace H4ck3r31\BankAccountExample\Domain\Model;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command;
use H4ck3r31\BankAccountExample\Domain\Model\Common\ReconstitutionException;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DebitTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DepositTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Common\CommandException;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountEventRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Iban\IbanEventRepository;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\Command\CommandHandler;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\Command\CommandHandlerTrait;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Common\Instantiable;

/**
 * CommandHandlerBundle
 */
final class CommandHandlerBundle implements Instantiable, CommandHandler
{
    use CommandHandlerTrait;

    /**
     * @return CommandHandlerBundle
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * @param Command\CreateAccountCommand $command
     * @throws CommandException
     */
    protected function handleCreateAccountCommand(Command\CreateAccountCommand $command)
    {
        $iban = Iban::assignAccountNumber(
            $command->getBank(),
            $command->getAccountNumber()
        );

        $account = Account::createAccount(
            $iban,
            $command->getAccountHolder()
        );

        IbanEventRepository::instance()->commit($iban);
        AccountEventRepository::instance()->commit($account);
    }

    /**
     * @param Command\ChangeAccountHolderCommand $command
     */
    protected function handleChangeAccountHolderCommand(Command\ChangeAccountHolderCommand $command)
    {
        $account = $this->fetchAccount($command);
        $account->changeAccountHolder($command->getAccountHolder());

        AccountEventRepository::instance()->commit($account);
    }

    /**
     * @param Command\DepositMoneyCommand $command
     */
    protected function handleDepositMoneyCommand(Command\DepositMoneyCommand $command)
    {
        $transaction = DepositTransaction::create(
            $command->getIban(),
            $command->getMoney(),
            $command->getReference(),
            $command->getAvailabilityDate()
        );

        $account = $this->fetchAccount($command);
        $account->attachDepositTransaction($transaction);

        AccountEventRepository::instance()->commit($account);
    }

    /**
     * @param Command\DebitMoneyCommand $command
     * @throws CommandException
     */
    protected function handleDebitMoneyCommand(Command\DebitMoneyCommand $command)
    {
        $transaction = DebitTransaction::create(
            $command->getIban(),
            $command->getMoney(),
            $command->getReference(),
            $command->getAvailabilityDate()
        );

        $account = $this->fetchAccount($command);
        $account->attachDebitTransaction($transaction);

        AccountEventRepository::instance()->commit($account);
    }

    /**
     * @param Command\CloseAccountCommand $command
     * @throws CommandException
     */
    protected function handleCloseAccountCommand(Command\CloseAccountCommand $command)
    {
        $account = $this->fetchAccount($command);
        $account->closeAccount();

        AccountEventRepository::instance()->commit($account);
    }

    /**
     * @param Command\AbstractAccountCommand $command
     * @return Account
     * @throws ReconstitutionException
     */
    private function fetchAccount(Command\AbstractAccountCommand $command)
    {
        $account = AccountEventRepository::instance()
            ->findByIban($command->getIban());

        if (!($account instanceof Account)) {
            throw new ReconstitutionException(
                'Could not reconstitute account',
                1477207653
            );
        }

        return $account;
    }
}
