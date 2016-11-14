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

use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\DataTransferObject;

/**
 * TransactionDto
 */
class TransactionDto implements DataTransferObject
{
    /**
     * @var bool
     */
    private $deposit;

    /**
     * @var bool
     */
    private $debit;

    /**
     * @var \DateTimeImmutable
     */
    private $entryDate = null;

    /**
     * @var \DateTimeImmutable
     */
    private $availabilityDate = null;

    /**
     * @var string
     */
    private $reference = '';

    /**
     * @var float
     */
    private $money = 0.0;

    /**
     * @return bool
     */
    public function isDeposit()
    {
        return $this->deposit;
    }

    /**
     * @param bool $deposit
     */
    public function setDeposit(bool $deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * @return bool
     */
    public function isDebit()
    {
        return $this->debit;
    }

    /**
     * @param bool $debit
     */
    public function setDebit(bool $debit)
    {
        $this->debit = $debit;
    }

    /**
     * @return \DateTimeImmutable $entryDate
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * @param \DateTimeImmutable $entryDate
     */
    public function setEntryDate(\DateTimeImmutable $entryDate)
    {
        $this->entryDate = $entryDate;
    }

    /**
     * @return \DateTimeImmutable $availabilityDate
     */
    public function getAvailabilityDate()
    {
        return $this->availabilityDate;
    }

    /**
     * @param \DateTimeImmutable $availabilityDate
     */
    public function setAvailabilityDate(\DateTimeImmutable $availabilityDate)
    {
        $this->availabilityDate = $availabilityDate;
    }

    /**
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return float $value
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money)
    {
        $this->money = $money;
    }
}
