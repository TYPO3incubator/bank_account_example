<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Transaction;

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
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountTcaProjectionRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction\TransactionTcaProjectionRepository;
use Ramsey\Uuid\Uuid;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Projection\Projection;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Projection\TcaProjectionService;

/**
 * TransactionTcaProjection
 */
final class TransactionTcaProjection implements Projection
{
    /**
     * @return string[]
     */
    public function listensTo()
    {
        return [
            AttachedDepositTransactionEvent::class,
            AttachedDebitTransactionEvent::class,
        ];
    }

    /**
     * @param BaseEvent $event
     */
    public function project(BaseEvent $event)
    {
        if ($event instanceof AttachedDepositTransactionEvent) {
            $this->projectAttachedDepositTransactionEvent($event);
        }
        if ($event instanceof AttachedDebitTransactionEvent) {
            $this->projectAttachedDebitTransactionEvent($event);
        }
    }

    /**
     * @param AttachedDepositTransactionEvent $event
     */
    private function projectAttachedDepositTransactionEvent(AttachedDepositTransactionEvent $event)
    {
        $accountData = AccountTcaProjectionRepository::instance()
            ->findByAggregateId($event->getAggregateId());

        $transactionData = TcaProjectionService::addAggregateId(
            Uuid::uuid4(),
            $event->getTransaction()->toArray()
        );
        $transactionData['account'] = $accountData['uid'];

        TransactionTcaProjectionRepository::instance()->add($transactionData);
    }

    /**
     * @param AttachedDebitTransactionEvent $event
     */
    private function projectAttachedDebitTransactionEvent(AttachedDebitTransactionEvent $event)
    {
        $accountData = AccountTcaProjectionRepository::instance()
            ->findByAggregateId($event->getAggregateId());

        $transactionData = TcaProjectionService::addAggregateId(
            Uuid::uuid4(),
            $event->getTransaction()->toArray()
        );
        $transactionData['account'] = $accountData['uid'];

        TransactionTcaProjectionRepository::instance()->add($transactionData);
    }
}
