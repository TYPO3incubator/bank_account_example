<?php
namespace H4ck3r31\BankAccountExample\Domain\Command;

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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountEventRepository;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\CommandApplicable;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\CommandHandlerTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

/**
 * CommandHandler
 */
class CommandHandler implements Instantiable, CommandApplicable
{
    use CommandHandlerTrait;

    /**
     * @return CommandHandler
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(CommandHandler::class);
    }

    /**
     * @param CreateCommand $command
     * @return \Generator
     * @throws CommandException
     */
    protected function onCreateCommand(CreateCommand $command)
    {
        Account::createdAccount($command->getHolder(), $command->getNumber());
    }

    /**
     * @param ChangeHolderCommand $command
     * @return \Generator
     */
    protected function onChangeHolderCommand(ChangeHolderCommand $command)
    {
        $this->fetchAccount($command)
            ->changedAccountHolder($command->getHolder());
    }

    /**
     * @param DepositCommand $command
     */
    protected function onDepositCommand(DepositCommand $command)
    {
        $this->fetchAccount($command)
            ->depositedAccount(
                $command->getValue(),
                $command->getReference(),
                $command->getAvailabilityDate()
            );
    }

    /**
     * @param DebitCommand $command
     * @throws CommandException
     */
    protected function onDebitCommand(DebitCommand $command)
    {
        $this->fetchAccount($command)
            ->debitedAccount(
                $command->getValue(),
                $command->getReference(),
                $command->getAvailabilityDate()
            );
    }

    /**
     * @param CloseCommand $command
     * @throws CommandException
     */
    protected function onCloseCommand(CloseCommand $command)
    {
        $this->fetchAccount($command)
            ->closedAccount();
    }

    /**
     * @param AbstractCommand $command
     * @return Account|null
     */
    protected function fetchAccount(AbstractCommand $command)
    {
        return AccountEventRepository::instance()
            ->findByUuid($command->getAccountId());
    }
}
