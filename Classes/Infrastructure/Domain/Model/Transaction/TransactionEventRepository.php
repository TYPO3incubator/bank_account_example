<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction;

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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\AbstractTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DebitTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DepositTransaction;
use H4ck3r31\BankAccountExample\Domain\Object\Transactional;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Repository\EventRepository;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Saga;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventStorePool;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\ProjectionManager;

/**
 * Repository organizing events for Transaction
 * @deprecated Transactions are handled in the bounds of Account
 */
class TransactionEventRepository implements EventRepository
{
    /**
     * @return TransactionEventRepository
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * @param UuidInterface $transactionId
     * @param string $eventId
     * @param string $type
     * @return AbstractTransaction
     */
    public function findByDepositTransactionId(UuidInterface $transactionId, string $eventId = '', string $type = Saga::EVENT_EXCLUDING)
    {
        $streamName = Common::STREAM_PREFIX_TRANSACTION . '/' . $transactionId->toString();
        $eventSelector = EventSelector::instance()->setStreamName($streamName);

        $transaction = Saga::instance()
            ->constraint($eventId, $type)
            ->tell(DepositTransaction::instance(), $eventSelector);

        return $transaction;
    }

    /**
     * @param UuidInterface $transactionId
     * @param string $eventId
     * @param string $type
     * @return AbstractTransaction
     */
    public function findByDebitTransactionId(UuidInterface $transactionId, string $eventId = '', string $type = Saga::EVENT_EXCLUDING)
    {
        $streamName = Common::STREAM_PREFIX_TRANSACTION . '/' . $transactionId->toString();
        $eventSelector = EventSelector::instance()->setStreamName($streamName);

        $transaction = Saga::instance()
            ->constraint($eventId, $type)
            ->tell(DebitTransaction::instance(), $eventSelector);

        return $transaction;
    }

    /**
     * @param AbstractTransaction $transaction
     * @deprecated Not required anymore
     */
    public function add(AbstractTransaction $transaction)
    {
        foreach ($transaction->getRecordedEvents() as $event) {
            $this->addEvent($event);
        }

        ProjectionManager::provide()->projectEvents($transaction->getRecordedEvents());
        $transaction->purgeRecordedEvents();
    }

    /**
     * @param BaseEvent|Transactional $event
     * @deprecated Not required anymore
     */
    public function addEvent(BaseEvent $event)
    {
        $streamName = Common::STREAM_PREFIX_TRANSACTION
            . '/' . $event->getTransactionId()->toString();

        $eventSelector = EventSelector::instance()
            ->setEvents([get_class($event)])
            ->setStreamName($streamName);

        EventStorePool::provide()
            ->getAllFor($eventSelector)
            ->attach($streamName, $event);
    }
}
