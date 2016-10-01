<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Bank;

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

use H4ck3r31\BankAccountExample\Infrastructure\Service\NationalBank;
use H4ck3r31\BankAccountExample\Infrastructure\Service\NationalBank\CoreDataFactory;

final class NationalBankRepository
{
    public static function instance()
    {
        return new static();
    }

    /**
     * @param string $nationalCode
     * @return BankRepository
     */
    public function findByNationalCode(string $nationalCode)
    {
        $coreData = CoreDataFactory::createFor($nationalCode);
        return new BankRepository($coreData);
    }
}
