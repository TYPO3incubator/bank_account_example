<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Service\NationalBank;

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

use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\AccountNumber;
use H4ck3r31\BankAccountExample\Domain\Model\Common\BranchCode;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Common\NationalCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\SubsidiaryCode;

abstract class AbstractCoreData
{
    /**
     * @return NationalCode
     */
    abstract public function getNationalCode();

    /**
     * @param string $branchCode
     * @return BranchCode
     */
    abstract public function convertBranchCode(string $branchCode);

    /**
     * @param string $subsidiaryCode
     * @return SubsidiaryCode
     */
    abstract public function convertSubsidiaryCode(string $subsidiaryCode);

    /**
     * @param string $accountNumber
     * @return AccountNumber
     */
    abstract public function convertAccountNumber(string $accountNumber);

    /**
     * @param string $searchBranchCode
     * @param string $searchSubsidiaryCode
     * @return null|Bank
     */
    abstract public function findBankByBranchAndSubsidiaryCode(
        string $searchBranchCode,
        string $searchSubsidiaryCode
    );

    /**
     * @param string $iban
     * @param bool $verify
     * @return Iban
     */
    abstract public function reconstituteIban(string $iban, bool $verify = true);
}
