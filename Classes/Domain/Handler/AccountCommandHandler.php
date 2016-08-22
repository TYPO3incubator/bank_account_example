<?php
namespace H4ck3r31\BankAccountExample\Domain\Handler;

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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountEventRepository;
use H4ck3r31\BankAccountExample\Domain\Repository\BankEventRepository;
use H4ck3r31\BankAccountExample\Service\BankService;

/**
 * AccountCommandHandler
 */
class AccountCommandHandler extends AbstractCommandHandler
{
    /**
     * @return AccountCommandHandler
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountCommandHandler::class);
    }

    /**
     * @var Account
     */
    protected $subject;

    /**
     * @param Account $subject
     * @return AccountCommandHandler
     */
    public function setSubject(Account $subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $holder
     * @param string $number
     * @return \Generator
     * @throws CommandException
     */
    public function createNew(string $holder, string $number = '')
    {
        $bankService = BankService::instance();

        $uuid = static::createUuid();
        $this->subject->_setUuid($uuid->toString());
        $this->subject->setHolder($holder);

        if (empty($number)) {
            $this->subject->setNumber(
                $bankService->createNewAccountNumber()
            );
        } elseif (!$bankService->hasAccountNumber($number)) {
            $this->subject->setNumber(
                $bankService->sanitizeAccountNumber($number)
            );
        } else {
            throw new CommandException('Number #' . $bankService->sanitizeAccountNumber($number) . ' is already assigned', 1471604553);
        }

        yield BankEventRepository::provide()
        => Event\AssignedAccountEvent::create($uuid);

        yield AccountEventRepository::provide()
        => Event\CreatedAccountEvent::create($uuid, $this->subject->getHolder(), $this->subject->getNumber());
    }

    /**
     * @return \Generator
     * @throws CommandException
     */
    public function close()
    {
        $this->checkClosed();
        if ((float)$this->subject->getBalance() !== 0.0) {
            throw new CommandException('Cannot close account since the balance is not zero', 1471473510);
        }

        $this->subject->setClosed(true);

        yield AccountEventRepository::provide()
        => Event\ClosedAccountEvent::create($this->subject->getUuidInterface());
    }

    /**
     * @param string $holder
     * @return \Generator
     */
    public function changeHolder(string $holder)
    {
        $this->checkClosed();

        if ($this->subject->getHolder() === $holder) {
            yield null;
        }

        $this->subject->setHolder($holder);

        yield AccountEventRepository::provide()
        => Event\ChangedAccountHolderEvent::create($this->subject->getUuidInterface(), $holder);
    }

    /**
     * @param float $value
     * @param string $reference
     * @param \DateTime|null $availabilityDate
     * @return \Generator
     * @throws CommandException
     */
    public function deposit(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $this->checkClosed();

        if ($value <= 0) {
            yield null;
        }

        $this->subject->setBalance(
            $this->subject->getBalance() + $value
        );

        $transaction = Transaction::instance();

        yield from TransactionCommandHandler::instance()
            ->setSubject($transaction)
            ->createNew($value, $reference, $availabilityDate);

        yield AccountEventRepository::provide()
        =>  Event\DepositedAccountEvent::create(
                $this->subject->getUuidInterface(),
                $transaction->getUuidInterface()
            );
    }

    /**
     * @param float $value
     * @param string $reference
     * @param \DateTime|null $availabilityDate
     * @return \Generator
     * @throws CommandException
     */
    public function debit(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $this->checkClosed();

        if ($value <= 0) {
            yield null;
        }
        $this->subject->setBalance(
            $this->subject->getBalance() - $value
        );

        if ($this->subject->getBalance() < 0) {
            throw new CommandException('Overdrawing account is not allowed', 1471604763);
        }

        $transaction = Transaction::instance();

        yield from TransactionCommandHandler::instance()
            ->setSubject($transaction)
            ->createNew(-$value, $reference, $availabilityDate);

        yield AccountEventRepository::provide()
        =>  Event\DebitedAccountEvent::create(
                $this->subject->getUuidInterface(),
                $transaction->getUuidInterface()
            );
    }

    /**
     * @throws CommandException
     */
    protected function checkClosed()
    {
        if ($this->subject->isClosed()) {
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
}
