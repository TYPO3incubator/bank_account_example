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
     * @param Command\AbstractCommand $command
     * @return CommandManager
     */
    public function manage(Command\AbstractCommand $command)
    {
        $commandName = ClassNamingUtility::getLastPart($command);
        $commandCallable = array($this, 'manage' . $commandName);

        if (is_callable($commandCallable)) {
            call_user_func($commandCallable, $command);
        }

        return $this;
    }

    protected function manageCreateCommand(Command\CreateCommand $command)
    {
        AccountRepository::instance()->addEvents(
            Account::create($command->getHolder(), $command->getNumber())->getEvents()
        );
    }
}
