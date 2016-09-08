<?php
namespace H4ck3r31\BankAccountExample\Domain\Object;

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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Money;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionReference;
use Ramsey\Uuid\UuidInterface;

/**
 * TransactionalTrait
 */
trait TransactionalTrait
{
    /**
     * @var UuidInterface
     */
    protected $transactionId;

    /**
     * @var Money
     */
    protected $money;

    /**
     * @var TransactionReference
     */
    protected $reference;

    /**
     * @var \DateTime
     */
    protected $entryDate;

    /**
     * @var \DateTime
     */
    protected $availabilityDate;

    /**
     * @return UuidInterface
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return TransactionReference
     */
    public function getReference(): TransactionReference
    {
        return $this->reference;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * @return null|\DateTimeImmutable
     */
    public function getAvailabilityDate()
    {
        return $this->availabilityDate;
    }
}
