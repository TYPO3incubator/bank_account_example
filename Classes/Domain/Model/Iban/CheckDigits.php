<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Iban;

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

use H4ck3r31\BankAccountExample\Domain\Model\Common\BranchCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\NationalCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\SubsidiaryCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\ValueObjectException;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Common\RepresentableAsString;

/**
 * CheckDigits
 */
class CheckDigits implements RepresentableAsString
{
    /**
     * @param string $checkDigits
     * @return CheckDigits
     */
    public static function create(string $checkDigits)
    {
        if (strlen($checkDigits) !== 2) {
            throw new ValueObjectException('Check digits length mismatch');
        }
        if (!is_numeric($checkDigits)) {
            throw new ValueObjectException('Check digits must be numeric');
        }

        return new static($checkDigits);
    }

    /**
     * @param NationalCode $nationalCode
     * @param BranchCode $branchCode
     * @param SubsidiaryCode $subsidiaryCode
     * @param AccountNumber $accountNumber
     * @return CheckDigits
     */
    public static function createFor(
        NationalCode $nationalCode,
        BranchCode $branchCode,
        SubsidiaryCode $subsidiaryCode,
        AccountNumber $accountNumber
    ) {
        $value = static::substituteCharacters(
            $branchCode . $subsidiaryCode . $accountNumber . $nationalCode . '00'
        );

        $checkValue = 98 - bcmod($value, 97);

        return static::create(
            str_pad($checkValue, 2, '0', STR_PAD_LEFT)
        );

    }

    /**
     * @param string $value
     * @return string
     */
    private static function substituteCharacters(string $value)
    {
        $substitutions = [];
        foreach (range('A', 'Z') as $character) {
            // character 'A' => 10, 'B' => 11, ...
            $substitutions[$character] = ord($character) - 55;
        }
        return str_replace(
            array_keys($substitutions),
            array_values($substitutions),
            $value
        );
    }

    /**
     * @var string
     */
    private $checkDigits;

    /**
     * @param string $checkDigits
     */
    private function __construct(string $checkDigits)
    {
        $this->checkDigits = $checkDigits;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->checkDigits;
    }

    /**
     * @param CheckDigits $checkDigits
     */
    public function verify(CheckDigits $checkDigits)
    {
        if ($this->checkDigits !== (string)$checkDigits) {
            throw new \RuntimeException('Invalid check digits');
        }
    }
}
