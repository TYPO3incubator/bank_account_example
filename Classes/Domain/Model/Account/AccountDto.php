<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account;

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

use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;

/**
 * AccountDto
 */
class AccountDto extends AbstractProjectableEntity
{
    /**
     * @var string
     */
    private $iban;

    /**
     * @var bool
     */
    private $closed = false;

    /**
     * @var string
     */
    private $accountHolder = '';

    /**
     * @var string
     */
    private $accountNumber = '';

    /**
     * @var float
     */
    private $balance = 0.0;

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban(string $iban)
    {
        $this->iban = $iban;
    }

    /**
     * @return boolean
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @param boolean $closed
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * @return string $holder
     */
    public function getAccountHolder()
    {
        return $this->accountHolder;
    }

    /**
     * @param string $accountHolder
     */
    public function setAccountHolder(string $accountHolder)
    {
        $this->accountHolder = $accountHolder;
    }

    /**
     * @return string $number
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber(string $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return float
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
}
