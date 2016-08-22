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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;

/**
 * Class TransactionEventHandler
 */
class TransactionEventHandler extends AbstractEventHandler
{
    /**
     * @param Transaction $subject
     * @return TransactionEventHandler
     */
    public static function create(Transaction $subject)
    {
        return Common::getObjectManager()->get(TransactionEventHandler::class, $subject);
    }

    /**
     * @param Transaction $subject
     */
    public function __construct(Transaction $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @var Transaction
     */
    protected $subject;

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
