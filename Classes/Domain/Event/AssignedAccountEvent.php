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
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

/**
 * AssignedAccountEvent
 */
class AssignedAccountEvent extends AbstractEvent implements Instantiable, RelationalEvent
{
    /**
     * @return AssignedAccountEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AssignedAccountEvent::class);
    }

    /**
     * @param UuidInterface $relationId Pointing to Account
     * @return AssignedAccountEvent
     */
    public static function create(UuidInterface $relationId)
    {
        $event = static::instance();
        $event->relationId = $relationId;
        return $event;
    }

    /**
     * @var UuidInterface
     */
    protected $relationId;

    /**
     * @return UuidInterface
     */
    public function getRelationId(): UuidInterface
    {
        return $this->relationId;
    }
}
