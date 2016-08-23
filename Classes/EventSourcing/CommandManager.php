<?php
namespace H4ck3r31\BankAccountExample\EventSourcing;

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
use H4ck3r31\BankAccountExample\Domain\Command;
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;
use TYPO3\CMS\DataHandling\Core\Utility\ClassNamingUtility;

/**
 * DepositCommand
 */
class CommandManager implements Instantiable
{
    /**
     * @return CommandManager
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(CommandManager::class);
    }

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager
     */
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param Command\AbstractCommand $command
     * @return CommandManager
     */
    public function manage(Command\AbstractCommand $command)
    {
        // clear session state (possibly modified subject)
        $this->persistenceManager->clearState();

        $commandName = ClassNamingUtility::getLastPart($command);
        $methodName = 'process' . $commandName;

        if (method_exists($this, $methodName)) {
            $this->{$methodName}($command);
        }

        return $this;
    }

    /**
     * @param Command\CreateCommand $command
     * @return \Generator
     * @throws \H4ck3r31\BankAccountExample\Domain\Object\CommandException
     */
    protected function processCreateCommand(Command\CreateCommand $command)
    {
        Account::createNew($command->getHolder(), $command->getNumber());
    }

    /**
     * @param Command\ChangeHolderCommand $command
     * @return \Generator
     */
    protected function processChangeHolderCommand(Command\ChangeHolderCommand $command)
    {
        $this->fetchAccount($command)
            ->changeHolder($command->getHolder());
    }

    /**
     * @param Command\DepositCommand $command
     */
    protected function processDepositCommand(Command\DepositCommand $command)
    {
        $this->fetchAccount($command)
            ->deposit(
                $command->getValue(),
                $command->getReference(),
                $command->getAvailabilityDate()
            );
    }

    /**
     * @param Command\DebitCommand $command
     * @throws \H4ck3r31\BankAccountExample\Domain\Object\CommandException
     */
    protected function processDebitCommand(Command\DebitCommand $command)
    {
        $this->fetchAccount($command)
            ->debit(
                $command->getValue(),
                $command->getReference(),
                $command->getAvailabilityDate()
            );
    }

    /**
     * @param Command\CloseCommand $command
     * @throws \H4ck3r31\BankAccountExample\Domain\Object\CommandException
     */
    protected function processCloseCommand(Command\CloseCommand $command)
    {
        $this->fetchAccount($command)
            ->close();
    }

    /**
     * @param Command\AbstractCommand $command
     * @return Account|null
     */
    protected function fetchAccount(Command\AbstractCommand $command)
    {
        return AccountRepository::instance()
            ->findByUuid($command->getAccountId());
    }
}
