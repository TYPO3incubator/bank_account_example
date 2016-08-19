<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Applicable;

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
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Domain\Repository\BankRepository;
use H4ck3r31\BankAccountExample\Domain\Repository\TransactionRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable;

/**
 * ApplicableAccount
 */
class ApplicableAccount extends Account implements Applicable
{
    /**
     * @return ApplicableAccount
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(ApplicableAccount::class);
    }

    /**
     * @param string $holder
     * @param string $number
     * @return Account
     */
    public static function create(string $holder, string $number = '')
    {
        $bank = BankRepository::instance()->fetch();

        $uuid = static::createUuid();
        $account = static::instance();
        $account->uuid = $uuid->toString();
        $account->holder = $holder;

        if (empty($number)) {
            $account->number = $bank->createNewAccountNumber();
        } elseif (!$bank->hasAccountNumber($number)) {
            $account->number = $bank->sanitizeAccountNumber($number);
        } else {
            throw new CommandException('Number #' . $bank->sanitizeAccountNumber($number) . ' is already assigned', 1471604553);
        }

        $account->recordEvent(
            Event\CreatedAccountEvent::create($uuid, $account->getHolder(), $account->getNumber())
        );

        return $account;
    }

    /**
     * @return Account
     */
    public function close()
    {
        $this->checkClosed();
        if ((float)$this->balance !== 0.0) {
            throw new CommandException('Cannot close account since the balance is not zero', 1471473510);
        }

        $this->closed = true;
        $this->recordEvent(
            Event\ClosedAccountEvent::create($this->getUuidInterface())
        );

        return $this;
    }

    /**
     * @param string $holder
     * @return Account
     */
    public function changeHolder(string $holder)
    {
        $this->checkClosed();

        if ($this->_getProperty('holder') !== $holder) {
            $this->holder = $holder;
            $this->recordEvent(
                Event\ChangedAccountHolderEvent::create($this->getUuidInterface(), $holder)
            );
        }

        return $this;
    }

    public function deposit(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $this->checkClosed();

        if ($value > 0) {
            $this->balance += $value;
            $transaction = ApplicableTransaction::create($value, $reference, $availabilityDate);
            $this->mergeEvents($transaction->getEvents());

            $this->recordEvent(
                Event\DepositedAccountEvent::create($this->getUuidInterface(), $transaction->getUuidInterface())
            );
        }

        return $this;
    }

    public function debit(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $this->checkClosed();

        if ($value > 0) {
            $this->balance -= $value;

            if ($this->balance < 0) {
                throw new CommandException('Overdrawing account is not allowed', 1471604763);
            }

            $transaction = ApplicableTransaction::create(-$value, $reference, $availabilityDate);
            $this->mergeEvents($transaction->getEvents());

            $this->recordEvent(
                Event\DebitedAccountEvent::create($this->getUuidInterface(), $transaction->getUuidInterface())
            );
        }

        return $this;
    }

    protected function checkClosed()
    {
        if ($this->isClosed()) {
            throw new CommandException('Account is already closed', 1471473509);
        }
    }

    protected function checkPositiveValue(float $value)
    {
        if ($value === 0 || $value < 0) {
            throw new CommandException('Value must be positive', 1471512371);
        }
    }

    /**
     * @param AbstractEvent $event
     */
    public function apply(AbstractEvent $event)
    {
        if ($event instanceof Event\CreatedAccountEvent) {
            $this->resetRevision();
            $this->incrementRevision();
            $this->uuid = $event->getAggregateId()->toString();
            $this->holder = $event->getHolder();
            $this->number = $event->getNumber();
            $this->balance = 0;
        }

        if ($event instanceof Event\ChangedAccountHolderEvent) {
            $this->incrementRevision();
            $this->holder = $event->getHolder();
        }

        if ($event instanceof Event\ClosedAccountEvent) {
            $this->incrementRevision();
            $this->closed = true;
        }

        if (
            $event instanceof Event\DepositedAccountEvent
            || $event instanceof Event\DebitedAccountEvent
        ) {
            $this->incrementRevision();
            $transaction = TransactionRepository::instance()->findByUuid(
                $event->getTransactionId()
            );
            $this->transactions->attach($transaction);
            $this->balance += $transaction->getValue();
        }
    }
}
