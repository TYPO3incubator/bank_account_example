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
 * Location
 */
class Location
{
    /**
     * @param string $zip
     * @param string $city
     * @return Location
     */
    public static function createLocation(string $zip, string $city)
    {
        if (empty($zip)) {
            throw new ValueObjectException('ZIP must not be empty');
        }
        if (empty($city)) {
            throw new ValueObjectException('City must not be empty');
        }

        return new static($zip, $city);
    }

    /**
     * @param string $zip
     * @param string $city
     */
    protected function __construct(string $zip, string $city)
    {
        $this->zip = $zip;
        $this->city = $city;
    }

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $city;

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
}
