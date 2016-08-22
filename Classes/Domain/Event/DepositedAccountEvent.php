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
use TYPO3\CMS\DataHandling\Core\Domain\Event\Definition\RelationalEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Event\Definition\RelationalEventTrait;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

/**
 * DepositedAccountEvent
 */
class DepositedAccountEvent extends AbstractAccountEvent implements Instantiable, RelationalEvent
{
    use RelationalEventTrait;

    /**
     * @return DepositedAccountEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(DepositedAccountEvent::class);
    }

    /**
     * @param UuidInterface $aggregateId
     * @param UuidInterface $relationId Pointing to Transaction
     * @return DepositedAccountEvent
     */
    public static function create(UuidInterface $aggregateId, UuidInterface $relationId)
    {
        $event = static::instance();
        $event->aggregateId = $aggregateId;
        $event->relationId = $relationId;
        return $event;
    }
}
