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

use H4ck3r31\BankAccountExample\Domain\Object\Holdable;
use H4ck3r31\BankAccountExample\Domain\Object\HoldableTrait;
use H4ck3r31\BankAccountExample\Domain\Object\Numbered;
use H4ck3r31\BankAccountExample\Domain\Object\NumberedTrait;
use H4ck3r31\BankAccountExample\Domain\Object\Transactional;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionalTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent as SuperAbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Event\Storable;

/**
 * AbstractEvent
 */
abstract class AbstractEvent extends SuperAbstractEvent implements Storable
{
    /**
     * @var UuidInterface
     */
    protected $accountId;

    /**
     * @return UuidInterface
     */
    public function getAccountId(): UuidInterface
    {
        return $this->accountId;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        $data = [
            'accountId' => $this->accountId,
        ];

        if ($this instanceof Numbered) {
            $data['number'] = $this->getNumber();
        }
        if ($this instanceof Holdable) {
            $data['holder'] = $this->getHolder();
        }
        if ($this instanceof Transactional) {
            $data['transaction'] = $this->transactionalToArray();
        }

        return $data;
    }

    public function importData($data)
    {
        $this->accountId = Uuid::fromString($data['accountId']);

        /** @var NumberedTrait $this */
        if ($this instanceof Numbered) {
            $this->number = $data['number'];
        }
        /** @var HoldableTrait $this */
        if ($this instanceof Holdable) {
            $this->holder = $data['holder'];
        }
        /** @var TransactionalTrait $this */
        if ($this instanceof Transactional) {
            $this->transactionalFromArray($data['transaction']);
        }
    }
}
