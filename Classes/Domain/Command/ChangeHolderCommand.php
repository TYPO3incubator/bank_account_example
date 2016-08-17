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
use H4ck3r31\BankAccountExample\Domain\Object\Holdable;
use H4ck3r31\BankAccountExample\Domain\Object\HoldableTrait;
use Ramsey\Uuid\UuidInterface;

/**
 * ChangeHolderCommand
 */
class ChangeHolderCommand extends AbstractCommand implements Holdable
{
    use HoldableTrait;

    /**
     * @return ChangeHolderCommand
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(ChangeHolderCommand::class);
    }

    /**
     * @var string
     */
    protected $holder;

    /**
     * @param UuidInterface $accountId
     * @param string $holder
     * @return ChangeHolderCommand
     */
    public static function create(UuidInterface $accountId, string $holder)
    {
        $command = static::instance();
        $command->accountId = $accountId;
        $command->holder = $holder;
        return $command;
    }
}
