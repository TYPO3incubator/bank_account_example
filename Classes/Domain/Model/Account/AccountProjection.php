<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\AttachedDebitTransactionEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\AttachedDepositTransactionEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\ChangedAccountHolderEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\ClosedAccountEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\CreatedAccountEvent;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountEventRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountProjectionRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Projection\Projection;
use TYPO3\CMS\DataHandling\DataHandling\Infrastructure\EventStore\Saga;

/**
 * AccountProjection
 */
final class AccountProjection implements Projection
{
    /**
     * @return string[]
     */
    public function listensTo()
    {
        return [
            CreatedAccountEvent::class,
            ChangedAccountHolderEvent::class,
            ClosedAccountEvent::class,
            AttachedDepositTransactionEvent::class,
            AttachedDebitTransactionEvent::class,
        ];
    }

    /**
     * @param BaseEvent $event
     */
    public function project(BaseEvent $event)
    {
        if ($event instanceof CreatedAccountEvent) {
            $this->projectCreatedAccountEvent($event);
        }
        if ($event instanceof ChangedAccountHolderEvent) {
            $this->projectChangedAccountHolderEvent($event);
        }
        if ($event instanceof ClosedAccountEvent) {
            $this->projectClosedAccountEvent($event);
        }
        if ($event instanceof AttachedDepositTransactionEvent) {
            $this->projectAttachedDepositTransactionEvent($event);
        }
        if ($event instanceof AttachedDebitTransactionEvent) {
            $this->projectAttachedDebitTransactionEvent($event);
        }
    }

    /**
     * @param CreatedAccountEvent $event
     */
    private function projectCreatedAccountEvent(CreatedAccountEvent $event)
    {
        $account = AccountEventRepository::instance()
            ->findByIban(
                $event->getIban(),
                $event->getEventId(),
                Saga::EVENT_INCLUDING
            );
        AccountProjectionRepository::instance()->add(
            $account->toArray()
        );
    }

    /**
     * @param ChangedAccountHolderEvent $event
     */
    private function projectChangedAccountHolderEvent(ChangedAccountHolderEvent $event)
    {
        AccountProjectionRepository::instance()->update(
            (string)$event->getIban(),
            ['accountHolder' => $event->getAccountHolder()->getValue()]
        );
    }

    /**
     * @param ClosedAccountEvent $event
     */
    private function projectClosedAccountEvent(ClosedAccountEvent $event)
    {
        AccountProjectionRepository::instance()->update(
            (string)$event->getIban(),
            ['closed' => true]
        );
    }

    /**
     * @param AttachedDepositTransactionEvent $event
     */
    private function projectAttachedDepositTransactionEvent(AttachedDepositTransactionEvent $event)
    {
        $account = AccountProjectionRepository::instance()
            ->findByIban($event->getIban());
        $account->applyEvent($event);

        AccountProjectionRepository::instance()->update(
            (string)$event->getIban(),
            ['balance' => $account->getBalance()]
        );
    }

    /**
     * @param AttachedDebitTransactionEvent $event
     */
    private function projectAttachedDebitTransactionEvent(AttachedDebitTransactionEvent $event)
    {
        $account = AccountProjectionRepository::instance()
            ->findByIban($event->getIban());
        $account->applyEvent($event);

        AccountProjectionRepository::instance()->update(
            (string)$event->getIban(),
            ['balance' => $account->getBalance()]
        );
    }
}
