<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Transaction;

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
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Event\CreatedDebitTransactionEvent;
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use Ramsey\Uuid\Uuid;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventApplicable;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventHandlerTrait;

/**
 * DebitTransaction
 */
class DebitTransaction extends AbstractTransaction implements EventApplicable
{
    use EventHandlerTrait;

    /**
     * @param array $data
     * @return DebitTransaction
     */
    public static function buildFromProjection(array $data)
    {
        $transaction = static::instance();
        $transaction->projected = true;
        $transaction->transactionId = Uuid::fromString($data['transactionId']);
        $transaction->iban = Iban::fromString($data['iban']);
        $transaction->money = Money::create($data['money']);
        $transaction->reference = TransactionReference::create($data['reference']);
        $transaction->entryDate = new \DateTimeImmutable($data['entryDate']);
        $transaction->availabilityDate = new \DateTimeImmutable($data['availabilityDate']);
        return $transaction;
    }

    /**
     * @return DebitTransaction
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(static::class);
    }

    /**
     * @return string
     */
    protected function getTransactionType()
    {
        return get_class($this);
    }


    /**
     * Command handlers
     */

    /**
     * @param Iban $iban
     * @param Money $money
     * @param TransactionReference $reference
     * @param \DateTime|null $availabilityDate
     * @return DebitTransaction
     * @throws CommandException
     */
    public static function createTransaction(
        Iban $iban,
        Money $money,
        TransactionReference $reference,
        \DateTime $availabilityDate = null
    ) {
        $entryDate = new \DateTime('now');

        if ($availabilityDate === null) {
            $availabilityDate = $entryDate;
        } elseif ($availabilityDate < $entryDate) {
            throw new CommandException('Availability date cannot be before entry date', 1471512962);
        }

        $transaction = static::instance();
        $transactionId = Uuid::uuid4();

        $event = CreatedDebitTransactionEvent::create(
            $iban,
            $transactionId,
            $money,
            $reference,
            $entryDate,
            $availabilityDate
        );
        $transaction->manageEvent($event);

        return $transaction;
    }


    /*
     * Event handling
     */

    /**
     * @param CreatedDebitTransactionEvent $event
     */
    protected function applyCreatedDebitTransactionEvent(CreatedDebitTransactionEvent $event)
    {
        $this->transactionId = $event->getTransactionId();
        $this->iban = $event->getIban();
        $this->money = $event->getMoney();
        $this->reference = $event->getReference();
        $this->entryDate = $event->getEntryDate();
        $this->availabilityDate = $event->getAvailabilityDate();
    }
}
