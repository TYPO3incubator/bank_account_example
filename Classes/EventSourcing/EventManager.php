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
use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Handler\AccountEventHandler;
use H4ck3r31\BankAccountExample\Domain\Handler\TransactionEventHandler;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Event\Definition\EntityEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;
use TYPO3\CMS\DataHandling\Core\Process\Projection\ProjectionPool;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;
use TYPO3\CMS\DataHandling\Extbase\Persistence\RepositoryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class EventManager implements Instantiable
{
    /**
     * @return EventManager
     */
    static public function instance()
    {
        return Common::getObjectManager()->get(EventManager::class);
    }

    /**
     * @param AbstractEvent $event
     */
    public function manage(AbstractEvent $event)
    {
        $concerning = EventSelector::instance()
            ->setEvents([get_class($event)]);

        try {
            $projection = ProjectionPool::provide()
                ->getFor($concerning);
        } catch (\Exception $exception) {
            return;
        }

        $projection->projectEvent($event);
    }
}
