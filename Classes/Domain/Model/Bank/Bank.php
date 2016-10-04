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
use H4ck3r31\BankAccountExample\Domain\Model\Iban\AccountNumber;
use H4ck3r31\BankAccountExample\Domain\Model\Common\BranchCode;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Common\NationalCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\SubsidiaryCode;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Event\EventHandlerTrait;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Common\RepresentableAsArray;

/**
 * Bank
 */
final class Bank implements RepresentableAsArray
{
    use EventHandlerTrait;

    /**
     * @param NationalCode $nationalCode
     * @param BranchCode $branchCode
     * @param SubsidiaryCode $subsidiaryCode
     * @param string $name
     * @param Location $location
     */
    public function __construct(
        NationalCode $nationalCode,
        BranchCode $branchCode,
        SubsidiaryCode $subsidiaryCode,
        string $name,
        Location $location
    ) {
        $this->nationalCode = $nationalCode;
        $this->branchCode = $branchCode;
        $this->subsidiaryCode = $subsidiaryCode;
        $this->name = $name;
        $this->location = $location;
    }

    /**
     * @var NationalCode
     */
    private $nationalCode;

    /**
     * @var BranchCode
     */
    private $branchCode;

    /**
     * @var SubsidiaryCode
     */
    private $subsidiaryCode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Location
     */
    private $location;

    public function toArray()
    {
        return [
            'nationalCode' => $this->nationalCode,
            'branchCode' => $this->branchCode,
            'subsidiaryCode' => $this->subsidiaryCode,
        ];
    }

    /**
     * @return NationalCode
     */
    public function getNationalCode()
    {
        return $this->nationalCode;
    }

    /**
     * @return BranchCode
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * @return SubsidiaryCode
     */
    public function getSubsidiaryCode()
    {
        return $this->subsidiaryCode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \H4ck3r31\BankAccountExample\Domain\Model\Bank\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param AccountNumber $accountNumber
     * @return Iban
     */
    public function compileIban(AccountNumber $accountNumber)
    {
        return Iban::create(
            $this->nationalCode,
            $this->branchCode,
            $this->subsidiaryCode,
            $accountNumber
        );
    }
}
