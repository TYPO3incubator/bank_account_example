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
use H4ck3r31\BankAccountExample\Domain\Object\CommandException;
use H4ck3r31\BankAccountExample\Domain\Repository\TransactionEventRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Handler\CommandApplicable;

/**
 * TransactionCommandHandler
 */
class TransactionCommandHandler implements CommandApplicable
{
    /**
     * @return TransactionCommandHandler
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(TransactionCommandHandler::class);
    }

    /**
     * @var Transaction
     */
    protected $subject;

    /**
     * @param Transaction $subject
     * @return TransactionCommandHandler
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param float $value
     * @param string $reference
     * @param \DateTime|null $availabilityDate
     * @return \Generator
     * @throws CommandException
     */
    public function createNew(float $value, string $reference, \DateTime $availabilityDate = null)
    {
        $uuid = \Ramsey\Uuid\Uuid::uuid4();
        $this->subject->_setProperty('uuid', $uuid->toString());

        $this->subject
            ->setValue($value)
            ->setReference($reference)
            ->setEntryDate(new \DateTime('now'));

        if ($availabilityDate === null) {
            $this->subject->setAvailabilityDate($this->subject->getEntryDate());
        } elseif ($availabilityDate >= $this->subject->getEntryDate()) {
            $this->subject->setAvailabilityDate($availabilityDate);
        } else {
            throw new CommandException('Availability date cannot be before entry date', 1471512962);
        }

        yield TransactionEventRepository::provide()
        =>  Event\CreatedTransactionEvent::create(
                $uuid,
                $this->subject->getValue(),
                $this->subject->getReference(),
                $this->subject->getEntryDate(),
                $this->subject->getAvailabilityDate()
            );
    }
}
