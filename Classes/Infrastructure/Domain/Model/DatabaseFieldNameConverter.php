<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DatabaseFieldNameConverter
 */
class DatabaseFieldNameConverter
{
    /**
     * @param array $data
     * @return array
     */
    public static function toDatabase(array $data)
    {
        foreach ($data as $key => $value) {
            $fieldName = GeneralUtility::camelCaseToLowerCaseUnderscored($key);
            if ($key !== $fieldName) {
                $data[$fieldName] = $value;
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function fromDatabase(array $data)
    {
        foreach ($data as $key => $value) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($key);
            if ($key !== $propertyName) {
                $data[$propertyName] = $value;
                unset($data[$key]);
            }
        }

        return $data;
    }
}
