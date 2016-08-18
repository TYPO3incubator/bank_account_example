<?php
namespace H4ck3r31\BankAccountExample\Domain\Command;

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
use H4ck3r31\BankAccountExample\Domain\Object\Transactional;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionalTrait;
use Ramsey\Uuid\UuidInterface;

/**
 * DepositCommand
 */
class DepositCommand extends AbstractCommand implements Transactional
{
    use TransactionalTrait;

    /**
     * @return DepositCommand
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(DepositCommand::class);
    }

    /**
     * @param UuidInterface $accountId
     * @param float $value
     * @param string $reference
     * @param null|\DateTime $availabilityDate
     * @return DepositCommand
     */
    public static function create(UuidInterface $accountId, float $value, string $reference = '', \DateTime $availabilityDate = null)
    {
        $command = static::instance();
        $command->accountId = $accountId;
        $command->value = $value;
        $command->reference = $reference;
        $command->entryDate = new \DateTime('now');
        $command->availabilityDate = ($availabilityDate ?: $command->entryDate);
        return $command;
    }
}
