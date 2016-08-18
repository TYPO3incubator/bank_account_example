<?php
namespace H4ck3r31\BankAccountExample\Domain\Transient;

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
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable;

/**
 * Bank
 */
class Bank implements Applicable
{
    /**
     * @return Bank
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(Bank::class);
    }

    /**
     * @var string
     */
    protected $accountNumbers = [];

    public function apply(AbstractEvent $event)
    {
        if ($event instanceof Event\CreatedAccountEvent) {
            $this->accountNumbers[] = $event->getNumber();
        }
    }

    /**
     * @param string $accountNumber
     * @return bool
     */
    public function hasAccountNumber(string $accountNumber)
    {
        $accountNumber = $this->sanitizeAccountNumber($accountNumber);
        return in_array($accountNumber, $this->accountNumbers);
    }

    /**
     * @return string
     */
    public function createNewAccountNumber()
    {
        $lastAccountNumber = 0;
        if (!empty($this->accountNumbers)) {
            $lastAccountNumber = (int)max($this->accountNumbers);
        }

        $newAccountNumber = $this->sanitizeAccountNumber(++$lastAccountNumber);
        $this->accountNumbers[] = $newAccountNumber;
        return $newAccountNumber;
    }

    /**
     * @param int|string $accountNumber
     * @return string
     */
    public function sanitizeAccountNumber($accountNumber)
    {
        $accountNumber = ltrim($accountNumber, '0');
        $accountNumber = str_pad($accountNumber, 10, '0', STR_PAD_LEFT);
        return $accountNumber;
    }
}
