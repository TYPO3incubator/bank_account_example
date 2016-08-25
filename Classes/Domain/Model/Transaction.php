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
use H4ck3r31\BankAccountExample\Domain\Repository\TransactionEventRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Handler\CommandHandlerTrait;
use TYPO3\CMS\DataHandling\Core\Domain\Handler\EventApplicable;
use TYPO3\CMS\DataHandling\Core\Domain\Handler\EventHandlerTrait;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;

/**
 * Transaction
 */
class Transaction extends AbstractProjectableEntity implements EventApplicable
{
    use CommandHandlerTrait;
    use EventHandlerTrait;

    /**
     * @return Transaction
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(Transaction::class);
    }

    /**
     * @var \DateTime
     */
    protected $entryDate = null;

    /**
     * @var \DateTime
     */
    protected $availabilityDate = null;

    /**
     * @var string
     */
    protected $reference = '';

    /**
     * @var float
     */
    protected $value = 0.0;

    /**
     * @return \DateTime $entryDate
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * @return \DateTime $availabilityDate
     */
    public function getAvailabilityDate()
    {
        return $this->availabilityDate;
    }

    /**
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return float $value
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Command handlers
     */

    /**
     * @param float $value
     * @param string $reference
     * @param \DateTime|null $availabilityDate
     * @return Transaction
     * @throws CommandException
     */
    public static function createdTransaction(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $entryDate = new \DateTime('now');

        if ($availabilityDate === null) {
            $availabilityDate = $entryDate;
        } elseif ($availabilityDate < $entryDate) {
            throw new CommandException('Availability date cannot be before entry date', 1471512962);
        }

        $transaction = static::instance();
        $uuid = static::createUuid();

        $event = Event\CreatedTransactionEvent::create(
            $uuid,
            $value,
            $reference,
            $entryDate,
            $availabilityDate
        );

        $transaction->apply($event);
        static::emitEvent(TransactionEventRepository::provide(), $event);

        return $transaction;
    }


    /*
     * Event handling
     */

    /**
     * @param Event\CreatedTransactionEvent $event
     */
    protected function onCreatedTransactionEvent(Event\CreatedTransactionEvent $event)
    {
        $this->uuid = $event->getAggregateId()->toString();
        $this->value = $event->getValue();
        $this->reference = $event->getReference();
        $this->entryDate = $event->getEntryDate();
        $this->availabilityDate = $event->getAvailabilityDate();
    }
}
