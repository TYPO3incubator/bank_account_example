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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Address;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\AccountNumber;
use H4ck3r31\BankAccountExample\Domain\Model\Common\BranchCode;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\CheckDigits;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Common\NationalCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\SubsidiaryCode;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

final class SwissCoreData extends AbstractCoreData
{
    const NATIONAL_CODE = 'CH';
    const LENGTH_IBAN = 21;
    const LENGTH_BRANCH_CODE = 5;
    const LENGTH_SUBSIDIARY_CODE = 0;
    const LENGTH_ACCOUNT_NUMBER = 12;

    /**
     * @return NationalCode
     */
    public function getNationalCode()
    {
        return NationalCode::create(static::NATIONAL_CODE);
    }

    /**
     * @param string $branchCode
     * @return BranchCode
     */
    public function convertBranchCode(string $branchCode)
    {
        return BranchCode::create(
            $branchCode,
            static::LENGTH_BRANCH_CODE,
            true
        );
    }

    /**
     * @param string $subsidiaryCode
     * @return SubsidiaryCode
     */
    public function convertSubsidiaryCode(string $subsidiaryCode)
    {
        return SubsidiaryCode::create(
            $subsidiaryCode,
            static::LENGTH_SUBSIDIARY_CODE,
            false
        );
    }

    /**
     * @param string $accountNumber
     * @return AccountNumber
     */
    public function convertAccountNumber(string $accountNumber)
    {
        return AccountNumber::create(
            $accountNumber,
            static::LENGTH_ACCOUNT_NUMBER,
            true
        );
    }

    /**
     * @return SubsidiaryCode
     */
    public function getSubsidiaryCode()
    {
        return $this->convertSubsidiaryCode('');
    }

    /**
     * @param string $searchBranchCode
     * @param string $searchSubsidiaryCode
     * @return null|Bank
     */
    public function findBankByBranchAndSubsidiaryCode(
        string $searchBranchCode,
        string $searchSubsidiaryCode
    ) {
        $bank = null;
        $searchBranchCode = $this->convertBranchCode($searchBranchCode);
        $filePath = ExtensionManagementUtility::extPath(Common::KEY_EXTENSION)
            . 'Resources/Private/CoreData/Swiss.txt';

        $file = fopen($filePath, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $line = mb_convert_encoding($line, 'utf-8', 'iso-8859-1');

            // $branchType = substr($line, 0, 2);
            $branchCodeOld = $this->convertBranchCode(
                trim(substr($line, 2, 5))
            );

            $branchCodeNew = trim(substr($line, 11, 5));
            if (!empty($branchCodeNew)) {
                $branchCodeNew = $this->convertBranchCode(
                    trim(substr($line, 11, 5))
                );
            }

            if (!empty($branchCodeNew) && (string)$branchCodeNew === (string)$searchBranchCode) {
                $branchCode = $branchCodeNew;
            } elseif ((string)$branchCodeOld === (string)$searchBranchCode) {
                $branchCode = $branchCodeOld;
            } else {
                continue;
            }

            $bankName = trim(substr($line, 54, 60));
            $street = trim(substr($line, 149, 35));
            $zipCode = trim(substr($line, 184, 10));
            $cityName = trim(substr($line, 194, 35));

            $address = Address::createAddress($street, $zipCode, $cityName);

            $bank = new Bank(
                NationalCode::create('CH'),
                $this->convertBranchCode($branchCode),
                $this->getSubsidiaryCode(),
                $bankName,
                $address
            );

            break;
        }
        fclose($file);

        return $bank;
    }

    /**
     * @param string $iban
     * @return Iban
     */
    public function reconstituteIban(string $iban) {
        if (strlen($iban) !== static::LENGTH_IBAN) {
            throw new \InvalidArgumentException('IBAN length mismatch');
        }

        $nationalCode = NationalCode::create(substr($iban, 0, 2));
        $this->getNationalCode()->verify($nationalCode);

        $branchCode = $this->convertBranchCode(
            substr($iban, 4, static::LENGTH_BRANCH_CODE)
        );
        $subsidiaryCode = $this->getSubsidiaryCode();
        $accountNumber = $this->convertAccountNumber(
            substr($iban, 4 + static::LENGTH_BRANCH_CODE, static::LENGTH_ACCOUNT_NUMBER)
        );
        $checkDigits = CheckDigits::create(
            substr($iban, 2, 2)
        );

        CheckDigits::createFor($nationalCode, $branchCode, $subsidiaryCode, $accountNumber)
            ->verify($checkDigits);

        return Iban::create(
            $nationalCode,
            $branchCode,
            $subsidiaryCode,
            $accountNumber
        );
    }
}
