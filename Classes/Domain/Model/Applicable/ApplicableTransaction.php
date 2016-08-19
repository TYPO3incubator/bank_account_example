<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Applicable;

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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable;

/**
 * ApplicableTransaction
 */
class ApplicableTransaction extends Transaction implements Applicable
{
    /**
     * @return ApplicableTransaction
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(ApplicableTransaction::class);
    }

    public static function create(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $uuid = static::createUuid();
        $transaction = static::instance();
        $transaction->uuid = $uuid->toString();
        $transaction->value = $value;
        $transaction->reference = $reference;
        $transaction->setEntryDate(new \DateTime('now'));

        if ($availabilityDate === null) {
            $transaction->availabilityDate = $transaction->getEntryDate();
        } elseif ($availabilityDate >= $transaction->getEntryDate()) {
            $transaction->availabilityDate = $availabilityDate;
        } else {
            throw new CommandException('Availability date cannot be before entry date', 1471512962);
        }

        $transaction->recordEvent(
            Event\CreatedTransactionEvent::create(
                $uuid,
                $transaction->getValue(),
                $transaction->getReference(),
                $transaction->getEntryDate(),
                $transaction->getAvailabilityDate()
            )
        );

        return $transaction;
    }

    public function apply(AbstractEvent $event)
    {
        if ($event instanceof Event\CreatedTransactionEvent) {
            $this->resetRevision();
            $this->incrementRevision();
            $this->uuid = $event->getAggregateId()->toString();
            $this->value = $event->getValue();
            $this->reference = $event->getReference();
            $this->entryDate = $event->getEntryDate();
            $this->availabilityDate = $event->getAvailabilityDate();
        }
    }
}
