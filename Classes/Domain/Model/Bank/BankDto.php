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

use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\DataTransferObject;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Common\RepresentableAsArray;

/**
 * BankDto
 */
class BankDto implements DataTransferObject, RepresentableAsArray
{
    /**
     * @var string
     */
    private $nationalCode;

    /**
     * @var string
     */
    private $branchCode;

    /**
     * @var string
     */
    private $subsidiaryCode;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'nationalCode' => $this->nationalCode,
            'branchCode' => $this->branchCode,
            'subsidiaryCode' => $this->subsidiaryCode,
        ];
    }

    /**
     * @return string
     */
    public function getNationalCode()
    {
        return $this->nationalCode;
    }

    /**
     * @param string $nationalCode
     */
    public function setNationalCode(string $nationalCode)
    {
        $this->nationalCode = $nationalCode;
    }

    /**
     * @return string
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * @param string $branchCode
     */
    public function setBranchCode(string $branchCode)
    {
        $this->branchCode = $branchCode;
    }

    /**
     * @return string
     */
    public function getSubsidiaryCode()
    {
        return $this->subsidiaryCode;
    }

    /**
     * @param string $subsidiaryCode
     */
    public function setSubsidiaryCode(string $subsidiaryCode)
    {
        $this->subsidiaryCode = $subsidiaryCode;
    }
}
