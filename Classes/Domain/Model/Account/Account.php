<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account;

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
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DebitTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DepositTransaction;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction\TransactionEventRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Iban\IbanEventRepository;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventApplicable;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventHandlerTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Object\RepresentableAsArray;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;

/**
 * Account
 */
class Account extends AbstractProjectableEntity implements EventApplicable, RepresentableAsArray
{
    use EventHandlerTrait;

    /**
     * @return Account
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(Account::class);
    }

    /**
     * @param array $data
     * @return Account
     */
    public static function buildFromProjection(array $data)
    {
        $account = static::instance();
        $account->projected = true;
        $account->iban = Iban::fromString($data['iban']);
        $account->closed = (bool)$data['closed'];
        $account->accountHolder = $data['accountHolder'];
        $account->balance = $data['balance'];
        return $account;
    }

    /**
     * @var Iban
     */
    private $iban;

    /**
     * @var bool
     */
    private $closed;

    /**
     * @var AccountHolder
     */
    private $accountHolder;

    /**
     * @var float
     */
    private $balance = 0.0;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'iban' => (string)$this->iban,
            'closed' => (int)$this->closed,
            'accountHolder' => (string)$this->accountHolder,
            'balance' => (float)$this->balance,
        ];
    }

    /**
     * @return Iban
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @return string
     */
    public function getAccountHolder()
    {
        return $this->accountHolder;
    }

    /**
     * @return float $balance
     */
    public function getBalance()
    {
        return $this->balance;
    }


    /**
     * Command handling
     */

    /**
     * @param Iban $iban
     * @param AccountHolder $accountHolder
     * @return Account
     * @throws CommandException
     */
    public static function createAccount(Iban $iban, AccountHolder $accountHolder) {
        $ibans = IbanEventRepository::instance();
        if ($ibans->findByIban($iban) !== null) {
            throw new CommandException('IBAN ' . (string)$iban . ' is already assigned', 1471604553);
        }

        $event = Event\CreatedAccountEvent::create($iban, $accountHolder);
        $account = static::instance();
        $account->manageEvent($event);

        return $account;
    }

    /**
     * @throws CommandException
     */
    public function closeAccount()
    {
        $this->checkClosed();

        if ((float)$this->balance !== 0.0) {
            throw new CommandException('Cannot close account since the balance is not zero', 1471473510);
        }

        $event = Event\ClosedAccountEvent::create($this->iban);
        $this->manageEvent($event);
    }

    /**
     * @param AccountHolder $accountHolder
     */
    public function changeAccountHolder(AccountHolder $accountHolder)
    {
        $this->checkClosed();

        if ((string)$this->accountHolder === (string)$accountHolder) {
            return;
        }

        $event = Event\ChangedAccountHolderEvent::create(
            $this->iban,
            $accountHolder
        );
        $this->manageEvent($event);
    }

    /**
     * @param DepositTransaction $transaction
     * @throws CommandException
     */
    public function attachDepositTransaction(DepositTransaction $transaction) {
        $this->checkClosed();

        $event = Event\AttachedDepositTransactionEvent::create(
            $this->iban,
            $transaction->getTransactionId()
        );

        $this->manageEvent($event);
    }

    /**
     * @param DebitTransaction $transaction
     * @throws CommandException
     */
    public function attachDebitTransaction(DebitTransaction $transaction) {
        $this->checkClosed();

        if ($this->balance - $transaction->getMoney() < 0) {
            throw new CommandException('Overdrawing account is not allowed', 1471604763);
        }

        $event = Event\AttachedDebitTransactionEvent::create(
            $this->iban,
            $transaction->getTransactionId()
        );

        $this->manageEvent($event);
    }

    /**
     * @throws CommandException
     */
    private function checkClosed()
    {
        if ($this->closed) {
            throw new CommandException('Account is already closed', 1471473509);
        }
    }


    /*
     * Event handling
     */

    protected function onCreatedAccountEvent(Event\CreatedAccountEvent $event)
    {
        $this->iban = $event->getIban();
        $this->accountHolder = $event->getAccountHolder();
        $this->balance = 0;
    }

    /**
     * @param Event\ChangedAccountHolderEvent $event
     */
    protected function onChangedAccountHolderEvent(Event\ChangedAccountHolderEvent $event)
    {
        $this->accountHolder = $event->getAccountHolder();
    }

    /**
     * @param Event\ClosedAccountEvent $event
     */
    protected function onClosedAccountEvent(Event\ClosedAccountEvent $event)
    {
        $this->closed = true;
    }

    /**
     * @param Event\AttachedDepositTransactionEvent $event
     */
    protected function onAttachedDepositTransactionEvent(Event\AttachedDepositTransactionEvent $event)
    {
        $transaction = TransactionEventRepository::instance()
            ->findByDepositTransactionId($event->getTransactionId());
        $this->balance += $transaction->getMoney()->getValue();
    }

    /**
     * @param Event\AttachedDebitTransactionEvent $event
     */
    protected function onDebitedAccountEvent(Event\AttachedDebitTransactionEvent $event)
    {
        $transaction = TransactionEventRepository::instance()
            ->findByDepositTransactionId($event->getTransactionId());
        $this->balance -= $transaction->getMoney()->getValue();
    }
}
