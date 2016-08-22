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
use H4ck3r31\BankAccountExample\Domain\Repository\TransactionRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;

/**
 * AccountEventHandler
 */
class AccountEventHandler extends AbstractEventHandler
{
    /**
     * @param Account $subject
     * @return AccountEventHandler
     */
    public static function create(Account $subject)
    {
        return Common::getObjectManager()->get(AccountEventHandler::class, $subject);
    }

    /**
     * @param Account $subject
     */
    public function __construct(Account $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @var Account
     */
    protected $subject;

    /**
     * @param AbstractEvent $event
     */
    public function apply(AbstractEvent $event)
    {
        if ($event instanceof Event\CreatedAccountEvent) {
            // @todo Rather set revision from EventStore
            $this->subject->_incrementRevision();
            $this->subject->_setProperty('uuid', $event->getAggregateId()->toString());
            $this->subject->setHolder($event->getHolder());
            $this->subject->setNumber($event->getNumber());
            $this->subject->setBalance(0);
        }

        if ($event instanceof Event\ChangedAccountHolderEvent) {
            $this->subject->_incrementRevision();
            $this->subject->setHolder($event->getHolder());
        }

        if ($event instanceof Event\ClosedAccountEvent) {
            $this->subject->_incrementRevision();
            $this->subject->setClosed(true);
        }

        if (
            $event instanceof Event\DepositedAccountEvent
            || $event instanceof Event\DebitedAccountEvent
        ) {
            $this->subject->_incrementRevision();

            // @todo Fetch from event repository
            $transaction = TransactionRepository::instance()->findByUuid(
                $event->getRelationId()
            );
            $this->subject->addTransaction($transaction);
            $this->subject->setBalance(
                $this->subject->getBalance() + $transaction->getValue()
            );
        }
    }
}
