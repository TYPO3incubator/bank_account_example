<?php
namespace H4ck3r31\BankAccountExample\Domain\Event;

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
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\EntityEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

/**
 * CreatedTransactionEvent
 */
class CreatedTransactionEvent extends AbstractTransactionEvent implements Instantiable, EntityEvent
{
    /**
     * @return CreatedTransactionEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(CreatedTransactionEvent::class);
    }

    /**
     * @param UuidInterface $aggregateId
     * @param float $value
     * @param string $reference
     * @param \DateTime $entryDate
     * @param \DateTime $availabilityDate
     * @return CreatedTransactionEvent
     */
    public static function create(UuidInterface $aggregateId, float $value, string $reference, \DateTime $entryDate, \DateTime $availabilityDate)
    {
        $event = static::instance();
        $event->aggregateId = $aggregateId;
        $event->value = $value;
        $event->reference = $reference;
        $event->entryDate = $entryDate;
        $event->availabilityDate = $availabilityDate;
        return $event;
    }
}
