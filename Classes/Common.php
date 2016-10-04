<?php
namespace H4ck3r31\BankAccountExample;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountTcaCommandFactory;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command;
use H4ck3r31\BankAccountExample\Domain\Model\CommandHandlerBundle;
use Ramsey\Uuid\Uuid;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Command\CommandBus;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Projection\ProjectionManager;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\TcaCommand\TcaCommandManager;

/**
 * Common
 */
class Common
{
    const KEY_EXTENSION = 'bank_account_example';
    const STREAM_PREFIX = 'H4ck3r31-BankAccountExample';

    const STREAM_PREFIX_BANK = self::STREAM_PREFIX . '/BankDto';
    const STREAM_PREFIX_ACCOUNT = self::STREAM_PREFIX . '/Account';
    const STREAM_PREFIX_TRANSACTION = self::STREAM_PREFIX . '/Transaction';

    const TCA_TABLE_NAME_ACCOUNT = 'tx_bankaccountexample_domain_model_account';
    const TCA_TABLE_NAME_TRANSACTION = 'tx_bankaccountexample_domain_model_transaction';

    /**
     * Defines TypoScript.
     */
    public static function defineSettings()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(
            \H4ck3r31\BankAccountExample\Domain\Property\TypeConverter\IbanTypeConverter::class
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . static::KEY_EXTENSION . '/Configuration/TypoScript/constants.txt">'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . static::KEY_EXTENSION . '/Configuration/TypoScript/setup.txt">'
        );
    }

    /**
     * Registers requirements for event sources processing with TYPO3.
     */
    public static function registerEventSources()
    {
        CommandBus::provide()->addHandlerBundle(
            CommandHandlerBundle::instance(), [
                Command\CreateAccountCommand::class,
                Command\ChangeAccountHolderCommand::class,
                Command\CloseAccountCommand::class,
                Command\DepositMoneyCommand::class,
                Command\DebitMoneyCommand::class,
            ]
        );

        ProjectionManager::provide()
            ->registerProjections([
                // regular view projections
                new \H4ck3r31\BankAccountExample\Domain\Model\Iban\IbanProjection(),
                new \H4ck3r31\BankAccountExample\Domain\Model\Account\AccountProjection(),
                new \H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionProjection(),
                // TYPO3 backend TCA projections
                new \H4ck3r31\BankAccountExample\Domain\Model\Account\AccountTcaProjection(),
                new \H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionTcaProjection(),
            ]);
    }

    /**
     * Registers TCA table commands to map backend actions
     * to accordant domain commands and events in the end.
     */
    public static function registerTableCommands()
    {
        $tcaAccountTable = TcaCommandManager::provide()
            ->for(static::TCA_TABLE_NAME_ACCOUNT)
            ->setDeniedPerDefault(true)
            ->setMapping([
                'closed' => true,
                'balance' => true,
                'iban' => true,
                'account_holder' => 'accountHolder',
            ]);
        $tcaAccountTable->onCreate()
            ->setAllowed(true)
            ->setFactoryName(AccountTcaCommandFactory::class)
            ->setProperties([
                'iban' => true,
                'account_holder' => true,
            ])
            ->forRelation('transactions')
                ->setAttachAllowed(true);
        $tcaAccountTable->onModify()
            ->setAllowed(true)
            ->setFactoryName(AccountTcaCommandFactory::class)
            ->setProperties([
                'account_holder' => true,
            ])
            ->forRelation('transactions')
                ->setAttachAllowed(true);
        $tcaAccountTable->onDelete()
            ->setAllowed(true)
            ->setFactoryName(AccountTcaCommandFactory::class);

        $tcaTransactionTable = TcaCommandManager::provide()
            ->for(static::TCA_TABLE_NAME_TRANSACTION)
            ->setDeniedPerDefault(true)
            ->setMapping([
                'account' => true,
                'money' => true,
                'reference' => true,
                'type' => 'transactionType',
                'transaction_id' => 'transactionId',
                'entry_date' => 'entryDate',
                'availability_date' => 'availabilityDate',
            ]);
        $tcaTransactionTable->onCreate()
            ->setAllowed(true)
            ->setParentRequired(true)
            ->setFactoryName(AccountTcaCommandFactory::class)
            ->setProperties([
                'type' => true,
                'money' => true,
                'reference' => true,
                'transaction_id' => function() { return Uuid::uuid4()->toString(); },
                'entry_date' => function() { return (new \DateTime('now'))->format(\DateTime::W3C); },
                'availability_date' => function() { return (new \DateTime('now'))->format(\DateTime::W3C); },
            ]);
    }

    /**
     * @return \TYPO3\CMS\Core\Database\Connection
     */
    public static function getDatabaseConnection()
    {
        return \TYPO3\CMS\DataHandling\Core\Database\ConnectionPool::instance()
            ->getOriginConnection();
    }
}
