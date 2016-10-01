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

class CoreDataFactory
{
    /**
     * @param string $nationalCode
     * @return AbstractCoreData
     */
    public static function createFor(string $nationalCode)
    {
        if ($nationalCode === 'DE') {
            return new GermanCoreData();
        }
        if ($nationalCode === 'CH') {
            return new SwissCoreData();
        }

        throw new \RuntimeException('Cannot find national bank for ' . $nationalCode);
    }
}
