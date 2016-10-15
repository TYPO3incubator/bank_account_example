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

use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountHolder;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Common\Holdable;
use H4ck3r31\BankAccountExample\Domain\Model\Common\HoldableTrait;

/**
 * ChangeAccountHolderCommand
 */
class ChangeAccountHolderCommand extends AbstractAccountCommand implements Holdable
{
    use HoldableTrait;

    /**
     * @return ChangeAccountHolderCommand
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * @param Iban $iban
     * @param AccountHolder $accountHolder
     * @return ChangeAccountHolderCommand
     */
    public static function create(Iban $iban, AccountHolder $accountHolder)
    {
        $command = static::instance();
        $command->iban = $iban;
        $command->accountHolder = $accountHolder;
        return $command;
    }
}
