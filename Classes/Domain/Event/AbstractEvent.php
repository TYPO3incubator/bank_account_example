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

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent as SuperAbstractEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\AggregateEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\AggregateEventTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\RelationalEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\RelationalEventTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\StorableEvent;

/**
 * AbstractEvent
 */
abstract class AbstractEvent extends SuperAbstractEvent implements StorableEvent
{
    /**
     * @var UuidInterface
     */
    protected $aggregateId;

    /**
     * @return UuidInterface
     */
    public function getAggregateId(): UuidInterface
    {
        return $this->aggregateId;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        $data = [];

        if ($this instanceof AggregateEvent) {
            $data['aggregateId'] = $this->getAggregateId();
        }
        if ($this instanceof RelationalEvent) {
            $data['relationId'] = $this->getRelationId();
        }

        return $data;
    }

    /**
     * @param array|null $data
     * @return void
     */
    public function importData($data)
    {
        /** @var AggregateEventTrait */
        if ($this instanceof AggregateEvent) {
            $this->aggregateId = Uuid::fromString($data['aggregateId']);
        }
        /** @var RelationalEventTrait $this */
        if ($this instanceof RelationalEvent) {
            $this->relationId = Uuid::fromString($data['relationId']);
        }
    }
}
