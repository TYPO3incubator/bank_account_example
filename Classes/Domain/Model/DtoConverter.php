<?php
namespace H4ck3r31\BankAccountExample\Domain\Model;

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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountDto;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\AbstractTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionDto;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Bank\NationalBankRepository;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\BankDto;

class DtoConverter
{
    /**
     * @param Iban $iban
     * @return BankDto
     */
    public static function ibanToBankDto(Iban $iban)
    {
        $bankDto = Common::getObjectManager()->get(BankDto::class);
        $bankDto->setNationalCode($iban->getNationalCode());
        $bankDto->setBranchCode($iban->getBranchCode());
        $bankDto->setSubsidiaryCode($iban->getSubsidiaryCode());
        return $bankDto;
    }

    /**
     * @param Bank $bank
     * @return BankDto
     */
    public static function toBankDto(Bank $bank)
    {
        $bankDto = Common::getObjectManager()->get(BankDto::class);
        $bankDto->setNationalCode($bank->getNationalCode());
        $bankDto->setBranchCode($bank->getBranchCode());
        $bankDto->setSubsidiaryCode($bank->getSubsidiaryCode());
        return $bankDto;
    }

    /**
     * @return \Closure
     */
    public static function toBankDtoClosure()
    {
        return function(Bank $bank)
        {
            return static::toBankDto($bank);
        };
    }

    /**
     * @param BankDto $bankDto
     * @return Bank|null
     */
    public static function fromBankDto(BankDto $bankDto)
    {
        return NationalBankRepository::instance()
            ->findByNationalCode($bankDto->getNationalCode())
            ->findByBranchAndSubsidiaryCode(
                $bankDto->getBranchCode(),
                $bankDto->getSubsidiaryCode()
            );
    }

    /**
     * @param Account $account
     * @return AccountDto
     */
    public static function toAccountDto(Account $account)
    {
        $accountDto = Common::getObjectManager()->get(AccountDto::class);
        $accountDto->setIban($account->getIban());
        $accountDto->setClosed($account->isClosed());
        $accountDto->setAccountHolder($account->getAccountHolder());
        $accountDto->setAccountNumber($account->getIban()->getAccountNumber());
        $accountDto->setBalance($account->getBalance());
        return $accountDto;
    }

    /**
     * @return \Closure
     */
    public static function toAccountDtoClosure()
    {
        return function(Account $account)
        {
            return static::toAccountDto($account);
        };
    }

    /**
     * @param AbstractTransaction $transaction
     * @return TransactionDto
     */
    public static function toTransactionDto(AbstractTransaction $transaction)
    {
        $transactionDto = Common::getObjectManager()->get(TransactionDto::class);
        $transactionDto->setDeposit($transaction->isDeposit());
        $transactionDto->setDebit($transaction->isDebit());
        $transactionDto->setMoney($transaction->getMoney()->getValue());
        $transactionDto->setReference($transaction->getReference()->getValue());
        $transactionDto->setEntryDate($transaction->getEntryDate());
        $transactionDto->setAvailabilityDate($transaction->getAvailabilityDate());
        return $transactionDto;
    }

    /**
     * @return \Closure
     */
    public static function toTransactionDtoClosure()
    {
        return function(AbstractTransaction $transaction)
        {
            return static::toTransactionDto($transaction);
        };
    }
}
