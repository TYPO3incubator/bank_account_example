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
use H4ck3r31\BankAccountExample\Domain\Object\Numbered;
use H4ck3r31\BankAccountExample\Domain\Object\NumberedTrait;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

/**
 * CreatedEvent
 */
class CreatedEvent extends AbstractEvent implements Instantiable, Numbered, Holdable
{
    use NumberedTrait;
    use HoldableTrait;

    /**
     * @return CreatedEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(CreatedEvent::class);
    }

    /**
     * @param UuidInterface $accountId
     * @param string $holder
     * @param string $number
     * @return CreatedEvent
     */
    public static function create(UuidInterface $accountId, string $holder, string $number)
    {
        $event = static::instance();
        $event->accountId = $accountId;
        $event->holder = $holder;
        $event->number = $number;
        return $event;
    }
}
