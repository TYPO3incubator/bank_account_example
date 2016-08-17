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
use H4ck3r31\BankAccountExample\Domain\Repository\BankRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractEventEntity;

/**
 * Account
 */
class Account extends AbstractEventEntity implements Applicable
{
    /**
     * @return Account
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(Account::class);
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
            throw new \RuntimeException('Number #' . $bank->sanitizeAccountNumber($number) . ' is already assigned');
        }

        $account->recordEvent(
            Event\CreatedEvent::create($uuid, $account->getHolder(), $account->getNumber())
        );

        return $account;
    }

    /**
     * @return Account
     */
    public function close()
    {
        if ($this->isClosed()) {
            throw new \RuntimeException('Account is already closed', 1471473509);
        }
        if ((float)$this->balance !== 0.0) {
            throw new \RuntimeException('Cannot close account since the balance is not zero', 1471473510);
        }

        $this->closed = true;
        $this->recordEvent(
            Event\ClosedEvent::create($this->getUuidInterface())
        );

        return $this;
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
     * @var double
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
        $this->transactions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    public function isClosed()
    {
        return $this->closed;
    }

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
     * @param string $holder
     */
    public function setHolder(string $holder)
    {
        $this->holder = $holder;
    }

    /**
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return double $balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param double $balance
     */
    public function setBalance(double $balance)
    {
        $this->balance = $balance;
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Transaction $transaction
     */
    public function addTransaction(\H4ck3r31\BankAccountExample\Domain\Model\Transaction $transaction)
    {
        $this->transactions->attach($transaction);
    }

    /**
     * @param \H4ck3r31\BankAccountExample\Domain\Model\Transaction $transactionToRemove The Transaction to be removed
     */
    public function removeTransaction(\H4ck3r31\BankAccountExample\Domain\Model\Transaction $transactionToRemove)
    {
        $this->transactions->detach($transactionToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction> $transactions
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\H4ck3r31\BankAccountExample\Domain\Model\Transaction> $transactions
     */
    public function setTransactions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @param AbstractEvent $event
     */
    public function apply(AbstractEvent $event)
    {
        if ($event instanceof Event\CreatedEvent) {
            $this->resetRevision();
            $this->incrementRevision();
            $this->uuid = $event->getAccountId();
            $this->holder = $event->getHolder();
            $this->number = $event->getNumber();
            $this->balance = 0;
        }

        if ($event instanceof Event\ClosedEvent) {
            $this->incrementRevision();
            $this->closed = true;
        }

        if ($event instanceof Event\ChangedHolderEvent) {
            $this->incrementRevision();
            $this->holder = $event->getHolder();
        }
    }
}
