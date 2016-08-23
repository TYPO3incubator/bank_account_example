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
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Domain\ValidationModel\Account;
use H4ck3r31\BankAccountExample\Domain\ValidationModel\Transaction;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * CommandController
 */
class CommandController extends ActionController
{
    /**
     * @inject
     * @var \H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository
     */
    protected $accountRepository;

    /**
     * @param Account $account
     */
    public function createAction(Account $account)
    {
        try {
            Command\CommandManager::instance()->manage(
                Command\CreateCommand::create($account->getHolder(), $account->getNumber())
            );
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('list', 'Account');
    }

    /**
     * @param Account $account
     */
    public function updateAction(Account $account)
    {
        try {
            Command\CommandManager::instance()->manage(
                Command\ChangeHolderCommand::create($account->getUuidInterface(), $account->getHolder())
            );
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('list', 'Account');
    }

    /**
     * @param Account $account
     * @param Transaction $transaction
     */
    public function depositAction(Account $account, Transaction $transaction)
    {
        try {
            Command\CommandManager::instance()->manage(
                Command\DepositCommand::create(
                    $account->getUuidInterface(),
                    $transaction->getValue(),
                    $transaction->getReference(),
                    $transaction->getAvailabilityDate()
                )
            );
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('show', 'Account', null, ['account' => $account]);
    }

    /**
     * @param Account $account
     * @param Transaction $transaction
     */
    public function debitAction(Account $account, Transaction $transaction)
    {
        try {
            Command\CommandManager::instance()->manage(
                Command\DebitCommand::create(
                    $account->getUuidInterface(),
                    $transaction->getValue(),
                    $transaction->getReference(),
                    $transaction->getAvailabilityDate()
                )
            );
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('show', 'Account', null, ['account' => $account]);
    }

    /**
     * @param Account $account
     */
    public function closeAction(Account $account)
    {
        try {
            Command\CommandManager::instance()->manage(
                Command\CloseCommand::create($account->getUuidInterface())
            );
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }
        $this->redirect('list', 'Account');
    }
}
