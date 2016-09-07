<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account\Command;

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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Money;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionReference;
use H4ck3r31\BankAccountExample\Domain\Object\Transactional;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionalTrait;

/**
 * DebitMoneyCommand
 */
class DebitMoneyCommand extends AbstractAccountCommand implements Transactional
{
    use TransactionalTrait;

    /**
     * @return DebitMoneyCommand
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(DebitMoneyCommand::class);
    }

    /**
     * @param Iban $iban
     * @param Money $money
     * @param TransactionReference $reference
     * @param null|\DateTime $availabilityDate
     * @return DebitMoneyCommand
     */
    public static function create(
        Iban $iban,
        Money $money,
        TransactionReference $reference,
        \DateTime $availabilityDate = null
    ) {
        $command = static::instance();
        $command->iban = $iban;
        $command->money = $money;
        $command->reference = $reference;
        $command->availabilityDate = ($availabilityDate ?: $command->entryDate);
        return $command;
    }
}
