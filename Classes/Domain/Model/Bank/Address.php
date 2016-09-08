<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Bank;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\Event;
use H4ck3r31\BankAccountExample\Domain\Object\ValueObjectException;

/**
 * Address
 */
class Address extends Location
{
    /**
     * @param string $street
     * @param string $zip
     * @param string $city
     * @return Address
     */
    public static function createAddress(string $street, string $zip, string $city)
    {
        if (empty($street)) {
            throw new ValueObjectException('Street must not be empty');
        }
        if (empty($zip)) {
            throw new ValueObjectException('ZIP must not be empty');
        }
        if (empty($city)) {
            throw new ValueObjectException('City must not be empty');
        }

        return new static($street, $zip, $city);
    }

    /**
     * @param string $street
     * @param string $zip
     * @param string $city
     */
    protected function __construct(string $street, string $zip, string $city)
    {
        $this->street = $street;
        parent::__construct($zip, $city);
    }

    /**
     * @var string
     */
    private $street;

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }
}
