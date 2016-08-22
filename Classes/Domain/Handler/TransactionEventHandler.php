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

use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Handler\EventApplicable;

/**
 * Class TransactionEventHandler
 */
class TransactionEventHandler implements EventApplicable
{
    /**
     * @var Transaction
     */
    protected $subject;

    /**
     * @param Transaction $subject
     * @return TransactionEventHandler
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param AbstractEvent $event
     */
    public function apply(AbstractEvent $event)
    {
        if ($event instanceof Event\CreatedTransactionEvent) {
            $this->subject->_incrementRevision();
            $this->subject->_setUuid($event->getAggregateId()->toString());

            $this->subject
                ->setValue($event->getValue())
                ->setReference($event->getReference())
                ->setEntryDate($event->getEntryDate())
                ->setAvailabilityDate($event->getAvailabilityDate());
        }
    }
}
