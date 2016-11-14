<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account\Event;

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

use H4ck3r31\BankAccountExample\Domain\Model\AbstractEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\AbstractTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Common\TransactionAttachable;
use H4ck3r31\BankAccountExample\Domain\Model\Common\TransactionAttachableTrait;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Common\Instantiable;

/**
 * AttachedDepositTransactionEvent
 */
class AttachedDepositTransactionEvent extends AbstractEvent implements Instantiable, TransactionAttachable
{
    use TransactionAttachableTrait;

    /**
     * @return AttachedDepositTransactionEvent
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * @param UuidInterface $aggregateId
     * @param Iban $iban
     * @param AbstractTransaction $transaction
     * @return AttachedDepositTransactionEvent
     */
    public static function create(UuidInterface $aggregateId, Iban $iban, AbstractTransaction $transaction)
    {
        $event = static::instance();
        $event->aggregateId = $aggregateId;
        $event->iban = $iban;
        $event->transaction = $transaction;
        return $event;
    }
}
