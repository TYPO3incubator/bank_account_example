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

use H4ck3r31\BankAccountExample\Domain\Command;
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use H4ck3r31\BankAccountExample\EventSourcing\CommandManager;
use Ramsey\Uuid\Uuid;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * AccountController
 */
class AccountController extends ActionController
{
    /**
     * @inject
     * @var \H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository
     */
    protected $accountRepository;

    /**
     * @return void
     */
    public function listAction()
    {
        $accounts = $this->accountRepository->findAll();
        $this->view->assign('accounts', $accounts);
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function showAction(Account $account)
    {
        $this->view->assign('transaction', Transaction::instance());
        $this->view->assign('account', $account);
    }

    /**
     * @return void
     */
    public function newAction()
    {
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function editAction(Account $account)
    {
        $this->view->assign('cleanAccount', $account->_getCleanProperties());
        $this->view->assign('account', $account);
    }
}
