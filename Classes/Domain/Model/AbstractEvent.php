<?php
namespace H4ck3r31\BankAccountExample\Domain\Model;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountHolder;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Money;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionReference;
use H4ck3r31\BankAccountExample\Domain\Object\Holdable;
use H4ck3r31\BankAccountExample\Domain\Object\HoldableTrait;
use H4ck3r31\BankAccountExample\Domain\Object\Transactional;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionalTrait;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionIdentifiable;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionIdentifiableTrait;
use Ramsey\Uuid\Uuid;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\StorableEvent;

/**
 * AbstractEvent
 */
abstract class AbstractEvent extends BaseEvent implements StorableEvent
{
    /**
     * @var Iban
     */
    protected $iban;

    /**
     * @return Iban
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        $data['iban'] = (string)$this->getIban();

        if ($this instanceof Holdable) {
            $data['accountHolder'] = $this->getAccountHolder()->getValue();
        }
        if ($this instanceof Transactional) {
            $data['money'] = $this->getMoney()->getValue();
            $data['reference'] = $this->getReference()->getValue();
            $data['entryDate'] = $this->getEntryDate()->format(\DateTime::W3C);
            $data['availabilityDate'] = $this->getAvailabilityDate()->format(\DateTime::W3C);
        }
        if ($this instanceof TransactionIdentifiable) {
            $data['transactionId'] = $this->getTransactionId()->toString();
        }

        return $data;
    }

    /**
     * @param array|null $data
     * @return void
     */
    public function importData($data)
    {
        $this->iban = Iban::fromString($data['iban']);

        /** @var HoldableTrait $this */
        if ($this instanceof Holdable) {
            $this->accountHolder = AccountHolder::create($data['accountHolder']);
        }
        /** @var TransactionalTrait $this */
        if ($this instanceof Transactional) {
            $this->money = Money::create($data['money']);
            $this->reference = TransactionReference::create($data['reference']);
            $this->entryDate = new \DateTimeImmutable($data['entryDate']);
            $this->availabilityDate = new \DateTimeImmutable($data['availabilityDate']);
        }
        /** @var TransactionIdentifiableTrait $this */
        if ($this instanceof TransactionIdentifiable) {
            $this->transactionId = Uuid::fromString($data['transactionId']);
        }
    }
}
