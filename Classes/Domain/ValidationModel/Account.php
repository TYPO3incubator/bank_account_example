<?php
namespace H4ck3r31\BankAccountExample\Domain\ValidationModel;

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

use H4ck3r31\BankAccountExample\Domain\Event;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;

/**
 * Account
 */
class Account extends AbstractProjectableEntity
{
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
}
