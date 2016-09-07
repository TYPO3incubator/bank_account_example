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

use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Infrastructure\Service\NationalBank\AbstractCoreData;

final class BankRepository
{
    public function __construct(AbstractCoreData $coreData)
    {
        $this->coreData = $coreData;
    }

    /**
     * @var AbstractCoreData
     */
    private $coreData;

    /**
     * @param string $branchCode
     * @param string $subsidiaryCode (optional)
     * @return Bank|null
     */
    public function findByBranchAndSubsidiaryCode(
        string $branchCode,
        string $subsidiaryCode = ''
    ) {
        return $this->coreData
            ->findBankByBranchAndSubsidiaryCode($branchCode, $subsidiaryCode);
    }
}
