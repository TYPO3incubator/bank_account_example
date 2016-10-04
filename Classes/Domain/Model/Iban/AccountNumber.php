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

use H4ck3r31\BankAccountExample\Domain\Object\ValueObjectException;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Common\RepresentableAsString;

/**
 * AccountNumber
 */
class AccountNumber implements RepresentableAsString
{
    /**
     * @param string $accountNumber
     * @param int $length
     * @param bool $numericOnly
     * @return AccountNumber
     */
    public static function create(string $accountNumber, int $length, bool $numericOnly)
    {
        if (strlen($accountNumber) > $length) {
            throw new ValueObjectException('Account number length exceeded');
        }
        if ($numericOnly && !is_numeric($accountNumber)) {
            throw new ValueObjectException('Account number must be numeric');
        }

        $accountNumber = str_pad($accountNumber, $length, '0', STR_PAD_LEFT);

        return new static($accountNumber);
    }

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @param string $accountNumber
     */
    private function __construct(string $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->accountNumber;
    }
}
