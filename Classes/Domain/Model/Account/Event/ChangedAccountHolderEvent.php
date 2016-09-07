<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account\Event;

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
use H4ck3r31\BankAccountExample\Domain\Model\AbstractEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountHolder;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Object\Holdable;
use H4ck3r31\BankAccountExample\Domain\Object\HoldableTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

/**
 * ChangedAccountHolderEvent
 */
class ChangedAccountHolderEvent extends AbstractEvent implements Instantiable, Holdable
{
    use HoldableTrait;

    /**
     * @return ChangedAccountHolderEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(ChangedAccountHolderEvent::class);
    }

    /**
     * @param Iban $iban
     * @param AccountHolder $accountHolder
     * @return ChangedAccountHolderEvent
     */
    public static function create(Iban $iban, AccountHolder $accountHolder)
    {
        $event = static::instance();
        $event->iban = $iban;
        $event->accountHolder = $accountHolder;
        return $event;
    }
}