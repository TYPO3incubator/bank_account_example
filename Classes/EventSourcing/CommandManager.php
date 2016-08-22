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
use H4ck3r31\BankAccountExample\Domain\Handler\AccountCommandHandler;
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository;
use H4ck3r31\BankAccountExample\Domain\Repository\EventRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
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
     * @var AccountCommandHandler
     */
    protected $commandHandler;

    /**
     * @param Command\AbstractCommand $command
     * @return CommandManager
     */
    public function manage(Command\AbstractCommand $command)
    {
        $commandName = ClassNamingUtility::getLastPart($command);
        $methodName = 'process' . $commandName;

        if (method_exists($this, $methodName)) {
            $this->commandHandler = AccountCommandHandler::instance();
            $this->{$methodName}($command);

            /**
             * @var EventRepository $repository
             * @var AbstractEvent $event
             */
            foreach ($this->{$methodName}($command) as $repository => $event) {
                if ($event === null) {
                    continue;
                }

                $repository->addEvent($event);
                EventManager::instance()->manage($event);
            }
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
        yield from $this->commandHandler
            ->setSubject(Account::instance())
            ->createNew($command->getHolder(), $command->getNumber());
    }

    /**
     * @param Command\ChangeHolderCommand $command
     * @return \Generator
     */
    protected function processChangeHolderCommand(Command\ChangeHolderCommand $command)
    {
        yield from $this->commandHandler
            ->setSubject($this->fetchAccount($command))
            ->changeHolder($command->getHolder());
    }

    /**
     * @param Command\DepositCommand $command
     * @return \Generator
     */
    protected function processDepositCommand(Command\DepositCommand $command)
    {
        yield from $this->commandHandler
            ->setSubject($this->fetchAccount($command))
            ->deposit(
                $command->getValue(),
                $command->getReference(),
                $command->getAvailabilityDate()
            );
    }

    /**
     * @param Command\DebitCommand $command
     * @return \Generator
     * @throws \H4ck3r31\BankAccountExample\Domain\Object\CommandException
     */
    protected function processDebitCommand(Command\DebitCommand $command)
    {
        yield from $this->commandHandler
            ->setSubject($this->fetchAccount($command))
            ->debit(
                $command->getValue(),
                $command->getReference(),
                $command->getAvailabilityDate()
            );
    }

    /**
     * @param Command\CloseCommand $command
     * @return \Generator
     * @throws \H4ck3r31\BankAccountExample\Domain\Object\CommandException
     */
    protected function processCloseCommand(Command\CloseCommand $command)
    {
        yield from $this->commandHandler
            ->setSubject($this->fetchAccount($command))
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
