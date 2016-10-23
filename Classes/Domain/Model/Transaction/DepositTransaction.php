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

use H4ck3r31\BankAccountExample\Domain\Model\Common\ValueObjectException;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use Ramsey\Uuid\Uuid;

/**
 * DepositTransaction
 */
class DepositTransaction extends AbstractTransaction
{
    /**
     * @param array $data
     * @return DepositTransaction
     */
    public static function fromArray(array $data)
    {
        $transaction = new static();
        $transaction->transactionId = Uuid::fromString($data['transactionId']);
        $transaction->iban = Iban::fromString($data['iban']);
        $transaction->money = Money::create($data['money']);
        $transaction->reference = TransactionReference::create($data['reference']);
        $transaction->entryDate = new \DateTimeImmutable($data['entryDate']);
        $transaction->availabilityDate = new \DateTimeImmutable($data['availabilityDate']);
        return $transaction;
    }

    /**
     * @param Iban $iban
     * @param Money $money
     * @param TransactionReference $reference
     * @param \DateTimeImmutable|null $availabilityDate
     * @return DepositTransaction
     */
    public static function create(
        Iban $iban,
        Money $money,
        TransactionReference $reference,
        \DateTimeImmutable $availabilityDate = null
    ) {
        $entryDate = new \DateTimeImmutable('now');

        if ($availabilityDate === null) {
            $availabilityDate = $entryDate;
        } elseif ($availabilityDate < $entryDate) {
            throw new ValueObjectException(
                'Availability date cannot be before entry date',
                1471512963
            );
        }

        $transaction = new static();
        $transaction->transactionId = Uuid::uuid4();
        $transaction->iban = $iban;
        $transaction->money = $money;
        $transaction->reference = $reference;
        $transaction->entryDate = $entryDate;
        $transaction->availabilityDate = $availabilityDate;
        return $transaction;
    }

    /**
     * @return string
     */
    protected function getTransactionType()
    {
        return get_class($this);
    }
}
