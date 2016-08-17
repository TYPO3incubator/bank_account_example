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
use H4ck3r31\BankAccountExample\EventSourcing\CommandManager;
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
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $bankAccount
     */
    public function showAction(Account $bankAccount)
    {
        $this->view->assign('bankAccount', $bankAccount);
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
    public function createAction(Account $account)
    {
        CommandManager::instance()->manage(
            Command\CreateCommand::create($account->getHolder(), $account->getNumber())
        );
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function editAction(Account $account)
    {
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function updateAction(Account $account)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->accountRepository->update($account);
        $this->redirect('list');
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $bankAccount
     */
    public function deleteAction(Account $bankAccount)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->accountRepository->remove($bankAccount);
        $this->redirect('list');
    }
}
