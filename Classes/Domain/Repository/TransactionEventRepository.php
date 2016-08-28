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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Repository\EventRepository;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Saga;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventStorePool;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Providable;

/**
 * The event repository for Transactions
 */
class TransactionEventRepository implements Providable, EventRepository
{
    /**
     * @var TransactionEventRepository
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
     * @return TransactionEventRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(TransactionEventRepository::class);
    }

    /**
     * @param UuidInterface $uuid
     * @param string $eventId
     * @param string $type
     * @return Transaction
     */
    public function findByUuid(UuidInterface $uuid, string $eventId = '', string $type = Saga::EVENT_EXCLUDING)
    {
        $streamName = Common::STREAM_PREFIX_TRANSACTION . '/' . $uuid->toString();
        $eventSelector = EventSelector::instance()->setStreamName($streamName);
        $transaction = Saga::instance()
            ->constraint($eventId, $type)
            ->tell(Transaction::instance(), $eventSelector);
        return TransactionRepository::instance()->makeProjectable($transaction);
    }

    /**
     * @param BaseEvent|SpecificEvent $event
     */
    public function addEvent(BaseEvent $event)
    {
        $streamName = Common::STREAM_PREFIX_TRANSACTION
            . '/' . $event->getAggregateId()->toString();

        $eventSelector = EventSelector::instance()
            ->setEvents([get_class($event)])
            ->setStreamName($streamName);

        EventStorePool::provide()
            ->getAllFor($eventSelector)
            ->attach($streamName, $event);
    }
}
