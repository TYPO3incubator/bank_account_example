<?php
namespace H4ck3r31\BankAccountExample\Domain\Repository;

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
use H4ck3r31\BankAccountExample\Domain\Event\AbstractEvent as SpecificEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Repository\EventRepository;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Saga;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventStorePool;
use TYPO3\CMS\DataHandling\Core\Object\Providable;

/**
 * The event repository for Accounts
 */
class AccountEventRepository implements Providable, EventRepository
{
    /**
     * @var AccountEventRepository
     */
    protected static $repository;

    /**
     * @param bool $force
     * @return static
     */
    public static function provide(bool $force = false)
    {
        if ($force || empty(static::$repository)) {
            static::$repository = static::instance();
        }
        return static::$repository;
    }

    /**
     * @return AccountEventRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountEventRepository::class);
    }

    /**
     * @param UuidInterface $uuid
     * @return Account
     */
    public function findByUuid(UuidInterface $uuid)
    {
        $streamName = Common::STREAM_PREFIX_ACCOUNT . '/' . $uuid->toString();
        $eventSelector = EventSelector::instance()->setStreamName($streamName);
        return Saga::instance()->tell(Account::instance(), $eventSelector);
    }

    /**
     * @param AbstractEvent|SpecificEvent $event
     */
    public function addEvent(AbstractEvent $event)
    {
        $uuid = $event->getAggregateId()->toString();
        $streamName = Common::STREAM_PREFIX_ACCOUNT . '/' . $uuid;

        $eventSelector = EventSelector::instance()
            ->setEvents([get_class($event)])
            ->setStreamName($streamName);

        EventStorePool::provide()
            ->getAllFor($eventSelector)
            ->attach($streamName, $event);
    }
}
