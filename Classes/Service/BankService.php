<?php
namespace H4ck3r31\BankAccountExample\Service;

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

use H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

class BankService implements Instantiable
{
    /**
     * @return BankService
     */
    static public function instance()
    {
        return GeneralUtility::makeInstance(BankService::class);
    }

    /**
     * @return string
     */
    public function createNewAccountNumber()
    {
        $lastAccountNumber = 0;

        $account = AccountRepository::instance()->findByMaximumNumber();
        if ($account !== null) {
            $lastAccountNumber = (int)$account->getNumber();
        }

        $newAccountNumber = $this->sanitizeAccountNumber(++$lastAccountNumber);
        return $newAccountNumber;
    }

    /**
     * @param string $accountNumber
     * @return bool
     */
    public function hasAccountNumber(string $accountNumber)
    {
        $accountNumber = $this->sanitizeAccountNumber($accountNumber);
        $account = AccountRepository::instance()->findByNumber($accountNumber);
        return ($account !== null);
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
