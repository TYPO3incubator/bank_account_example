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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Event;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractEventEntity;

/**
 * Account
 */
class Account extends AbstractEventEntity
{
    /**
     * @return Account
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(Account::class);
    }

    /**
     * @var bool
     */
    protected $closed;

    /**
     * @var string
     */
    protected $holder = '';

    /**
     * @var string
     */
    protected $number = '';

    /**
     * @var float
     */
    protected $balance = 0.0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction>
     * @cascade remove
     */
    protected $transactions = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     */
    protected function initStorageObjects()
    {
        $this->transactions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     */
    public function setClosed(bool $closed)
    {
        $this->closed = $closed;
    }

    /**
     * @return string $holder
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @param string $holder
     */
    public function setHolder(string $holder)
    {
        $this->holder = $holder;
    }

    /**
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return float $balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance)
    {
        $this->balance = $balance;
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Transaction $transaction
     */
    public function addTransaction(\H4ck3r31\BankAccountExample\Domain\Model\Transaction $transaction)
    {
        $this->transactions->attach($transaction);
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Transaction $transactionToRemove The Transaction to be removed
     */
    public function removeTransaction(\H4ck3r31\BankAccountExample\Domain\Model\Transaction $transactionToRemove)
    {
        $this->transactions->detach($transactionToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction> $transactions
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction> $transactions
     */
    public function setTransactions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $transactions)
    {
        $this->transactions = $transactions;
    }
}
