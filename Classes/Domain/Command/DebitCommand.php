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

/**
 * DebitCommand
 */
class DebitCommand extends AbstractCommand implements Transactional
{
    use TransactionalTrait;

    /**
     * @return DebitCommand
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(DebitCommand::class);
    }

    /**
     * @param string $aggregateId
     * @param double $value
     * @param string $reference
     * @param null|\DateTime $availabilityDate
     * @return DebitCommand
     */
    public static function create(string $aggregateId, double $value, string $reference = '', \DateTime $availabilityDate = null)
    {
        $command = static::instance();
        $command->aggregateId = $aggregateId;
        $command->value = $value;
        $command->reference = $reference;
        $command->entryDate = new \DateTime('now');
        $command->availabilityDate = ($availabilityDate ?: $command->entryDate);
        return $command;
    }
}
