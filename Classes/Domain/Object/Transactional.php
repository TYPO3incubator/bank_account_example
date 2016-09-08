<?php
namespace H4ck3r31\BankAccountExample\Domain\Object;

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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Money;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionReference;
use Ramsey\Uuid\UuidInterface;

/**
 * Transactional
 */
interface Transactional
{
    /**
     * @return UuidInterface
     */
    public function getTransactionId();

    /**
     * @return Money
     */
    public function getMoney(): Money;

    /**
     * @return TransactionReference
     */
    public function getReference(): TransactionReference;

    /**
     * @return \DateTimeImmutable
     */
    public function getEntryDate();

    /**
     * @return null|\DateTimeImmutable
     */
    public function getAvailabilityDate();
}
