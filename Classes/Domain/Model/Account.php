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
use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountEventRepository;
use H4ck3r31\BankAccountExample\Domain\Repository\BankEventRepository;
use H4ck3r31\BankAccountExample\Domain\Repository\TransactionEventRepository;
use H4ck3r31\BankAccountExample\Service\BankService;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\CommandHandlerTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventApplicable;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventHandlerTrait;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Account
 */
class Account extends AbstractProjectableEntity implements EventApplicable
{
    use CommandHandlerTrait;
    use EventHandlerTrait;

    /**
     * @return Account
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(Account::class);
    }

    /**
     * @var bool
     */
    protected $closed;

    /**
     * @var string
     */
    protected $holder = '';

    /**
     * @var string
     */
    protected $number = '';

    /**
     * @var float
     */
    protected $balance = 0.0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction>
     * @cascade remove
     */
    protected $transactions = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     */
    protected function initStorageObjects()
    {
        $this->transactions = new ObjectStorage();
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     */
    public function setClosed(bool $closed)
    {
        $this->closed = $closed;
    }

    /**
     * @return string $holder
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return float $balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction> $transactions
     */
    public function getTransactions()
    {
        return $this->transactions;
    }


    /**
     * Command handling
     */

    /**
     * @param string $holder
     * @param string $number
     * @return Account
     * @throws CommandException
     */
    public static function createdAccount(string $holder, string $number = '')
    {
        $bankService = BankService::instance();

        if (empty($number)) {
            $number = $bankService->createNewAccountNumber();
        } elseif (!$bankService->hasAccountNumber($number)) {
            $number = $bankService->sanitizeAccountNumber($number);
        } else {
            throw new CommandException('Number #' . $bankService->sanitizeAccountNumber($number) . ' is already assigned', 1471604553);
        }

        $account = static::instance();
        $uuid = static::createUuid();

        static::emitEvent(
            BankEventRepository::provide(),
            Event\AssignedAccountEvent::create($uuid)
        );

        $event = Event\CreatedAccountEvent::create($uuid, $holder, $number);

        $account->apply($event);
        static::emitEvent(AccountEventRepository::provide(), $event);

        return $account;
    }

    /**
     * @throws CommandException
     */
    public function closedAccount()
    {
        $this->checkClosed();

        if ((float)$this->balance !== 0.0) {
            throw new CommandException('Cannot close account since the balance is not zero', 1471473510);
        }

        $event = Event\ClosedAccountEvent::create($this->getUuidInterface());

        $this->apply($event);
        static::emitEvent(AccountEventRepository::provide(), $event);
    }

    /**
     * @param string $holder
     */
    public function changedAccountHolder(string $holder)
    {
        $this->checkClosed();

        if ($this->holder === $holder) {
            return;
        }

        $event = Event\ChangedAccountHolderEvent::create($this->getUuidInterface(), $holder);

        $this->apply($event);
        static::emitEvent(AccountEventRepository::provide(), $event);
    }

    /**
     * @param float $value
     * @param string $reference
     * @param \DateTime|null $availabilityDate
     * @throws CommandException
     */
    public function depositedAccount(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $this->checkClosed();
        $this->checkPositiveValue($value);

        $transaction = Transaction::createdTransaction(
            $value,
            $reference,
            $availabilityDate
        );

        $event = Event\DepositedAccountEvent::create(
            $this->getUuidInterface(),
            $transaction->getUuidInterface()
        );

        $this->apply($event);
        static::emitEvent(AccountEventRepository::provide(), $event);
    }

    /**
     * @param float $value
     * @param string $reference
     * @param \DateTime|null $availabilityDate
     * @throws CommandException
     */
    public function debitedAccount(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $this->checkClosed();
        $this->checkPositiveValue($value);

        if ($this->balance - $value < 0) {
            throw new CommandException('Overdrawing account is not allowed', 1471604763);
        }

        $transaction = Transaction::createdTransaction(
            -$value,
            $reference,
            $availabilityDate
        );

        $event = Event\DebitedAccountEvent::create(
            $this->getUuidInterface(),
            $transaction->getUuidInterface()
        );

        $this->apply($event);
        static::emitEvent(AccountEventRepository::provide(), $event);
    }

    /**
     * @throws CommandException
     */
    protected function checkClosed()
    {
        if ($this->closed) {
            throw new CommandException('Account is already closed', 1471473509);
        }
    }

    /**
     * @param float $value
     * @throws CommandException
     */
    protected function checkPositiveValue(float $value)
    {
        if ($value === 0 || $value < 0) {
            throw new CommandException('Value must be positive', 1471512371);
        }
    }


    /*
     * Event handling
     */

    protected function onCreatedAccountEvent(Event\CreatedAccountEvent $event)
    {
        $this->uuid = $event->getAggregateId()->toString();
        $this->holder = $event->getHolder();
        $this->number = $event->getNumber();
        $this->balance =0;
    }

    protected function onChangedAccountHolderEvent(Event\ChangedAccountHolderEvent $event)
    {
        $this->holder = $event->getHolder();
    }

    protected function onClosedAccountEvent(Event\ClosedAccountEvent $event)
    {
        $this->closed = true;
    }

    protected function onDepositedAccountEvent(Event\DepositedAccountEvent $event)
    {
        $transaction = TransactionEventRepository::instance()->findByUuid(
            $event->getRelationId()
        );

        $this->transactions->attach($transaction);
        $this->balance += $transaction->getValue();
    }

    /**
     * @param Event\DebitedAccountEvent $event
     */
    protected function onDebitedAccountEvent(Event\DebitedAccountEvent $event)
    {
        $transaction = TransactionEventRepository::instance()->findByUuid(
            $event->getRelationId()
        );

        $this->transactions->attach($transaction);
        $this->balance += $transaction->getValue();
    }
}
