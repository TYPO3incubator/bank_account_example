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

use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Event\CreatedDebitTransactionEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Event\CreatedDepositTransactionEvent;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction\TransactionProjectionRepository;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\Projection;

/**
 * TransactionProjection
 */
final class TransactionProjection implements Projection
{
    /**
     * @return string[]
     */
    public function listensTo()
    {
        return [
            CreatedDepositTransactionEvent::class,
            CreatedDebitTransactionEvent::class,
        ];
    }

    /**
     * @param BaseEvent $event
     */
    public function project(BaseEvent $event)
    {
        if ($event instanceof CreatedDepositTransactionEvent) {
            $this->projectCreatedDepositTransactionEvent($event);
        }
        if ($event instanceof CreatedDebitTransactionEvent) {
            $this->projectCreatedDebitTransactionEvent($event);
        }
    }

    /**
     * @param CreatedDepositTransactionEvent $event
     */
    private function projectCreatedDepositTransactionEvent(CreatedDepositTransactionEvent $event)
    {
        $transaction = DepositTransaction::instance();
        $transaction->applyEvent($event);

        TransactionProjectionRepository::instance()->add(
            $transaction->toArray()
        );
    }

    /**
     * @param CreatedDebitTransactionEvent $event
     */
    private function projectCreatedDebitTransactionEvent(CreatedDebitTransactionEvent $event)
    {
        $transaction = DebitTransaction::instance();
        $transaction->applyEvent($event);

        TransactionProjectionRepository::instance()->add(
            $transaction->toArray()
        );
    }
}
