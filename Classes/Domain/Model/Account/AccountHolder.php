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

use H4ck3r31\BankAccountExample\Domain\Object\ValueObjectException;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Common\RepresentableAsString;

/**
 * AccountHolder
 */
class AccountHolder implements RepresentableAsString
{
    /**
     * @param string $value
     * @return AccountHolder
     */
    public static function create(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new ValueObjectException('Account holder must not be empty');
        }

        return new static($value);
    }

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
