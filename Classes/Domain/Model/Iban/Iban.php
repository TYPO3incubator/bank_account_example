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

use H4ck3r31\BankAccountExample\Domain\Model\Common\BranchCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\NationalCode;
use H4ck3r31\BankAccountExample\Domain\Model\Common\SubsidiaryCode;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Event\AssignedAccountNumberEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Iban\IbanEventRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Service\NationalBank\CoreDataFactory;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\Event\EventApplicable;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\Event\EventHandlerTrait;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Common\RepresentableAsArray;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Common\RepresentableAsString;
use TYPO3\CMS\Extbase\Mvc\Exception\CommandException;

/**
 * Iban
 */
class Iban implements EventApplicable, RepresentableAsString, RepresentableAsArray
{
    use EventHandlerTrait;

    /**
     * @param NationalCode $nationalCode
     * @param BranchCode $branchCode
     * @param SubsidiaryCode $subsidiaryCode
     * @param AccountNumber $accountNumber
     * @return Iban
     */
    public static function create(
        NationalCode $nationalCode,
        BranchCode $branchCode,
        SubsidiaryCode $subsidiaryCode,
        AccountNumber $accountNumber
    ) {
        return new Iban($nationalCode, $branchCode, $subsidiaryCode, $accountNumber);
    }

    /**
     * @param Bank $bank
     * @param string $accountNumber
     * @return Iban
     * @throws CommandException
     */
    public static function assignAccountNumber(Bank $bank, string $accountNumber)
    {
        $coreData = CoreDataFactory::createFor($bank->getNationalCode());
        $ibans = IbanEventRepository::instance();

        if (empty($accountNumber)) {
            $iban = $ibans->determineNextByBank($bank);
        } else {
            $accountNumber = $coreData->convertAccountNumber($accountNumber);
            $iban = $bank->compileIban($accountNumber);
        }

        if ($ibans->findByIban($iban) !== null) {
            throw new CommandException('IBAN ' . (string)$iban . ' is already assigned', 1471604553);
        }

        $event = AssignedAccountNumberEvent::create($iban);
        $iban->manageEvent($event);

        return $iban;
    }

    /**
     * @param string $iban
     * @param bool $verify
     * @return Iban
     */
    public static function fromString(string $iban, bool $verify = true)
    {
        $nationalCode = substr($iban, 0, 2);
        return CoreDataFactory::createFor($nationalCode)
            ->reconstituteIban($iban, $verify);
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
     * @var AccountNumber
     */
    private $accountNumber;

    /**
     * @param NationalCode $nationalCode
     * @param BranchCode $branchCode
     * @param SubsidiaryCode $subsidiaryCode
     * @param AccountNumber $accountNumber
     */
    private function __construct(
        NationalCode $nationalCode,
        BranchCode $branchCode,
        SubsidiaryCode $subsidiaryCode,
        AccountNumber $accountNumber
    ) {
        $this->nationalCode = $nationalCode;
        $this->branchCode = $branchCode;
        $this->subsidiaryCode = $subsidiaryCode;
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->nationalCode
        . $this->getCheckDigits()
        . $this->branchCode
        . $this->subsidiaryCode
        . $this->accountNumber;
    }

    /**
     * @return string[]
     */
    public function toArray()
    {
        return [
            'nationalCode' => (string)$this->nationalCode,
            'branchCode' => (string)$this->branchCode,
            'subsidiaryCode' => (string)$this->subsidiaryCode,
            'accountNumber' => (string)$this->accountNumber,
            'iban' => (string)$this,
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
     * @return CheckDigits
     */
    public function getCheckDigits()
    {
        return CheckDigits::createFor(
            $this->nationalCode,
            $this->branchCode,
            $this->subsidiaryCode,
            $this->accountNumber
        );
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
     * @return AccountNumber
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param Bank $bank
     * @return bool
     */
    public function belongsTo(Bank $bank)
    {
        return (
            (string)$this->nationalCode === (string)$bank->getNationalCode()
            && (string)$this->branchCode === (string)$bank->getBranchCode()
            && (string)$this->subsidiaryCode === (string)$bank->getSubsidiaryCode()
        );
    }
}
