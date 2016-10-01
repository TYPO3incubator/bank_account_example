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

use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountHolder;
use H4ck3r31\BankAccountExample\Domain\Object\Holdable;
use H4ck3r31\BankAccountExample\Domain\Object\HoldableTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

/**
 * CreateAccountCommand
 */
class CreateAccountCommand extends AbstractAccountCommand implements Instantiable, Holdable
{
    use HoldableTrait;

    /**
     * @return CreateAccountCommand
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * @var Bank
     */
    private $bank;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @param Bank $bank
     * @param AccountHolder $accountHolder
     * @param string|null $accountNumber
     * @return CreateAccountCommand
     */
    public static function create(Bank $bank, AccountHolder $accountHolder, string $accountNumber)
    {
        $command = static::instance();
        $command->bank = $bank;
        $command->accountHolder = $accountHolder;
        $command->accountNumber = $accountNumber;
        return $command;
    }

    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }
}
