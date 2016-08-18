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

/**
 * AbstractEvent
 */
abstract class AbstractEvent extends SuperAbstractEvent
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
        $data = [
            'aggregateId' => $this->aggregateId,
        ];

        return $data;
    }

    /**
     * @param array|null $data
     * @return void
     */
    public function importData($data)
    {
        $this->aggregateId = Uuid::fromString($data['aggregateId']);
    }
}
