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
     * Trigger projection of accounts.
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeAction()
    {
        if (
            $this->arguments->hasArgument('account')
            && $this->arguments->getArgument('account')->getDataType() === Account::class
        ) {
            $uid = (int)$this->request->getArgument('account');
            if ($uid === 0) {
                return;
            }
            $account = $this->accountRepository->findByUid($uid);
            if (!empty($account) && !empty($account->getUuid())) {
                $this->accountRepository->projectByUuid(
                    Uuid::fromString($account->getUuid())
                );
            } else {
                $this->accountRepository->buildAll();
            }
        }
    }

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
    public function createAction(Account $account)
    {
        try {
            CommandManager::instance()->manage(
                Command\CreateCommand::create($account->getHolder(), $account->getNumber())
            );
        } catch (\Exception $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('list');
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function editAction(Account $account)
    {
        $this->view->assign('cleanAccount', $account->_getCleanProperties());
        $this->view->assign('account', $account);
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function updateAction(Account $account)
    {
        try {
            CommandManager::instance()->manage(
                Command\ChangeHolderCommand::create($account->getUuidInterface(), $account->getHolder())
            );
        } catch (\Exception $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('list');
    }

    /**
     * @param Account $account
     * @param Transaction $transaction
     */
    public function depositAction(Account $account, Transaction $transaction)
    {
        try {
            CommandManager::instance()->manage(
                Command\DepositCommand::create(
                    $account->getUuidInterface(),
                    $transaction->getValue(),
                    $transaction->getReference(),
                    $transaction->getAvailabilityDate()
                )
            );
        } catch (\Exception $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('show', null, null, ['account' => $account]);
    }

    /**
     * @param Account $account
     * @param Transaction $transaction
     */
    public function debitAction(Account $account, Transaction $transaction)
    {
        try {
            CommandManager::instance()->manage(
                Command\DebitCommand::create(
                    $account->getUuidInterface(),
                    $transaction->getValue(),
                    $transaction->getReference(),
                    $transaction->getAvailabilityDate()
                )
            );
        } catch (\Exception $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('show', null, null, ['account' => $account]);
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Account $account
     */
    public function closeAction(Account $account)
    {
        try {
            CommandManager::instance()->manage(
                Command\CloseCommand::create($account->getUuidInterface())
            );
        } catch (\Exception $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('list');
    }
}
