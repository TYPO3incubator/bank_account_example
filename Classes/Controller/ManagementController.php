<?php
namespace H4ck3r31\BankAccountExample\Controller;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\Command;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionDto;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Bank\NationalBankRepository;
use H4ck3r31\BankAccountExample\Domain\Model\DtoConverter;
use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountDto;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\BankDto;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountProjectionRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction\TransactionProjectionRepository;

/**
 * ManagementController
 */
class ManagementController extends AbstractController
{
    /**
     * @return void
     */
    public function listBanksAction()
    {
        $nationalBankRepository = NationalBankRepository::instance();

        $banks = [];
        $banks[] = $nationalBankRepository
            ->findByNationalCode('CH')
            ->findByBranchAndSubsidiaryCode('04842');
        $banks[] = $nationalBankRepository
            ->findByNationalCode('DE')
            ->findByBranchAndSubsidiaryCode('68452290');

        $this->view->assign('banks', $banks);
    }

    /**
     * @param BankDto $bankDto
     * @return void
     */
    public function listAccountsAction(BankDto $bankDto)
    {
        $bank = DtoConverter::fromBankDto($bankDto);
        $accountDtos = array_map(
            DtoConverter::toAccountDtoClosure(),
            AccountProjectionRepository::instance()->findByBank($bank)
        );

        $this->view->assign('bank', $bank);
        $this->view->assign('accountDtos', $accountDtos);
    }

    /**
     * @param BankDto $bankDto
     * @param AccountDto $accountDto
     * @return void
     */
    public function newAction(BankDto $bankDto, AccountDto $accountDto = null)
    {
        $this->view->assign('bankDto', $bankDto);
        $this->view->assign('accountDto', $accountDto);
    }

    /**
     * @param Iban $iban
     */
    public function showAction(Iban $iban)
    {
        $bankDto = DtoConverter::ibanToBankDto($iban);
        $accountDto = DtoConverter::toAccountDto(
            AccountProjectionRepository::instance()->findByIban($iban)
        );

        $transactions = array_map(
            DtoConverter::toTransactionDtoClosure(),
            TransactionProjectionRepository::instance()->findByIban($iban)
        );

        $this->view->assign('bankDto', $bankDto);
        $this->view->assign('accountDto', $accountDto);
        $this->view->assign('transactions', $transactions);
        $this->view->assign('transactionDto', new TransactionDto());
    }
    /**
     * @param Iban $iban
     */
    public function editAction(Iban $iban)
    {
        $account = AccountProjectionRepository::instance()->findByIban($iban);
        $accountDto = DtoConverter::toAccountDto($account);
        $bankDto = DtoConverter::ibanToBankDto($iban);

        $this->view->assign('bankDto', $bankDto);
        $this->view->assign('account', $account);
        $this->view->assign('accountDto', $accountDto);
    }
}
