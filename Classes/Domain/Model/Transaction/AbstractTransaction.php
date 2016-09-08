<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Transaction;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\Event;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventApplicable;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Model\AggregateEntity;
use TYPO3\CMS\DataHandling\Core\Framework\Object\RepresentableAsArray;

/**
 * AbstractTransaction
 */
abstract class AbstractTransaction implements EventApplicable, AggregateEntity, RepresentableAsArray
{
    /**
     * @param array $data
     * @return AbstractTransaction
     */
    public static function buildFromProjection(array $data)
    {
        /** @var AbstractTransaction $transactionType */
        $transactionType = $data['transactionType'];
        return $transactionType::buildFromProjection($data);
    }

    /**
     * @var UuidInterface
     */
    protected $aggregateId;

    /**
     * @var UuidInterface
     */
    protected $transactionId;

    /**
     * @var Iban
     */
    protected $iban;

    /**
     * @var \DateTimeImmutable
     */
    protected $entryDate = null;

    /**
     * @var \DateTimeImmutable
     */
    protected $availabilityDate = null;

    /**
     * @var TransactionReference
     */
    protected $reference;

    /**
     * @var Money
     */
    protected $money;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'transactionId' => $this->transactionId->toString(),
            'transactionType' => $this->getTransactionType(),
            'iban' => (string)$this->iban,
            'money' => $this->money->getValue(),
            'reference' => $this->reference->getValue(),
            'entryDate' => $this->entryDate->format(\DateTime::W3C),
            'availabilityDate' => $this->availabilityDate->format(\DateTime::W3C),
        ];
    }

    /**
     * @return UuidInterface
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     * @return UuidInterface
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return Iban
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @return \DateTimeImmutable $entryDate
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * @return \DateTimeImmutable $availabilityDate
     */
    public function getAvailabilityDate()
    {
        return $this->availabilityDate;
    }

    /**
     * @return TransactionReference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @return bool
     */
    public function isDeposit()
    {
        return ($this instanceof DepositTransaction);
    }

    /**
     * @return bool
     */
    public function isDebit()
    {
        return ($this instanceof DebitTransaction);
    }

    /**
     * @return string
     */
    abstract protected function getTransactionType();
}
