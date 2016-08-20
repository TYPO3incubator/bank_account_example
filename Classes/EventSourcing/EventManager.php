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
use H4ck3r31\BankAccountExample\Domain\Event\CreatedAccountEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Manageable;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Stream\StreamProvider;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

class EventManager implements Instantiable, Manageable
{
    /**
     * @return EventManager
     */
    static public function instance()
    {
        return GeneralUtility::makeInstance(EventManager::class);
    }

    /**
     * @param AbstractEvent $event
     * @param array $categories
     */
    public function manage(AbstractEvent $event, array $categories = [])
    {
        if ($event instanceof CreatedAccountEvent) {
            StreamProvider::provide()->useStream(Common::NAME_BANK)->commit($event, $categories);
        }
        StreamProvider::provide()->useStream(Common::NAME_ACCOUNT)->commit($event, $categories);
    }

    /**
     * @param AbstractEvent[] $events
     */
    public function manageAll(array $events)
    {
        foreach ($events as $event) {
            $this->manage($event);
        }
    }
}
