<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account;

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
use H4ck3r31\BankAccountExample\Domain\Model\AbstractEvent as SpecificEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Repository\EventRepository;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Saga;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventStorePool;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\ProjectionManager;

/**
 * The event repository for IbanProjection
 */
class AccountEventRepository implements EventRepository
{
    /**
     * @var AccountEventRepository
     */
    protected static $repository;

    /**
     * @return AccountEventRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountEventRepository::class);
    }

    /**
     * @param Iban $iban
     * @param string $eventId
     * @param string $type
     * @return Account
     */
    public function findByIban(Iban $iban, string $eventId = '', string $type = Saga::EVENT_EXCLUDING)
    {
        $streamName = Common::STREAM_PREFIX_ACCOUNT . '/' . (string)$iban;
        $eventSelector = EventSelector::instance()->setStreamName($streamName);
        $account = Saga::instance()
            ->constraint($eventId, $type)
            ->tell(Account::instance(), $eventSelector);
        return $account;
    }

    public function add(Account $account)
    {
        foreach ($account->getRecordedEvents() as $event) {
            $this->addEvent($event);
        }

        ProjectionManager::provide()->projectEvents($account->getRecordedEvents());
        $account->purgeRecordedEvents();
    }

    /**
     * @param BaseEvent|SpecificEvent $event
     */
    public function addEvent(BaseEvent $event)
    {
        $iban = (string)$event->getIban();
        $streamName = Common::STREAM_PREFIX_ACCOUNT . '/' . $iban;

        $eventSelector = EventSelector::instance()
            ->setEvents([get_class($event)])
            ->setStreamName($streamName);

        EventStorePool::provide()
            ->getAllFor($eventSelector)
            ->attach($streamName, $event);
    }
}
