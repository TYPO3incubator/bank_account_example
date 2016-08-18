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
use H4ck3r31\BankAccountExample\Domain\Object\Holdable;
use H4ck3r31\BankAccountExample\Domain\Object\HoldableTrait;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

/**
 * ChangedHolderEvent
 */
class ChangedHolderEvent extends AbstractEvent implements Instantiable, Holdable
{
    use HoldableTrait;

    /**
     * @return ChangedHolderEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(ChangedHolderEvent::class);
    }

    /**
     * @param UuidInterface $aggregateId
     * @param string $holder
     * @return ChangedHolderEvent
     */
    public static function create(UuidInterface $aggregateId, string $holder)
    {
        $event = static::instance();
        $event->aggregateId = $aggregateId;
        $event->holder = $holder;
        return $event;
    }
}
