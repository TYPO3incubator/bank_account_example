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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Model\AbstractEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionIdentifiable;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionIdentifiableTrait;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

/**
 * AttachedDepositTransactionEvent
 */
class AttachedDepositTransactionEvent extends AbstractEvent implements Instantiable, TransactionIdentifiable
{
    use TransactionIdentifiableTrait;

    /**
     * @return AttachedDepositTransactionEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(static::class);
    }

    /**
     * @param Iban $iban
     * @param UuidInterface $transactionId
     * @return AttachedDepositTransactionEvent
     */
    public static function create(Iban $iban, UuidInterface $transactionId)
    {
        $event = static::instance();
        $event->iban = $iban;
        $event->transactionId = $transactionId;
        return $event;
    }
}
