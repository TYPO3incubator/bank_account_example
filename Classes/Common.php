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
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Event\AssignedAccountNumberEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\AttachedDebitTransactionEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Event\AttachedDepositTransactionEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionTcaCommandFactory;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountEventRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction\TransactionEventRepository;
use Ramsey\Uuid\Uuid;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\RelationalEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\SourceManager;
use TYPO3\CMS\DataHandling\Core\Framework\Process\CommandBus;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\ProjectionManager;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\ProjectionPool;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Tca\TcaCommandManager;
use TYPO3\CMS\DataHandling\Extbase\Persistence\EntityProjectionProvider;
use TYPO3\CMS\DataHandling\Extbase\Persistence\EntityStreamProjection;
use TYPO3\CMS\DataHandling\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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


        // build up TypoScript mappings for Extbase
        // (currently not used, since Extbase is just used for dispatching and view assignment)
        ExtensionUtility::instance()
            ->addMapping('tx_bankaccountexample_domain_model_account', Account::class)
            ->addMapping('tx_bankaccountexample_domain_model_transaction', \H4ck3r31\BankAccountExample\Domain\Model\Transaction\DepositTransaction::class)
            ->addMapping('tx_bankaccountexample_domain_model_transaction', \H4ck3r31\BankAccountExample\Domain\Model\Transaction\DebitTransaction::class);

        // tell the system that these database tables are somehow event-sourced
        // (currently not used, maybe this will be merged with ProjectionPool)
        SourceManager::provide()
            ->addSourcedTableName('tx_bankaccountexample_domain_model_account')
            ->addSourcedTableName('tx_bankaccountexample_domain_model_transaction');
    }

    /**
     * Registers TCA table commands to map backend actions
     * to accordant domain commands and events in the end.
     */
    public static function registerTableCommands()
    {
        $tcaAccountTable = TcaCommandManager::provide()
            ->for(static::TCA_TABLE_NAME_ACCOUNT)
            ->setMapping([
                'closed' => true,
                'balance' => true,
                'iban' => true,
                'account_holder' => 'accountHolder',
            ]);
        $tcaAccountTable->create()
            ->setAllowed(true)
            ->setFactory(new AccountTcaCommandFactory('create'))
            ->setProperties([
                'iban' => true,
                'account_holder' => true,
            ])
            ->forRelation('transactions')
                ->setAttachAllowed(true);
        $tcaAccountTable->modify()
            ->setAllowed(true)
            ->setFactory(new AccountTcaCommandFactory('modify'))
            ->setProperties([
                'account_holder' => true,
            ])
            ->forRelation('transactions')
                ->setAttachAllowed(true);
        $tcaAccountTable->delete()
            ->setAllowed(true)
            ->setFactory(new AccountTcaCommandFactory('delete'));

        $tcaTransactionTable = TcaCommandManager::provide()
            ->for(static::TCA_TABLE_NAME_TRANSACTION)
            ->setMapping([
                'account' => true,
                'money' => true,
                'reference' => true,
                'type' => 'transactionType',
                'transaction_id' => 'transactionId',
                'entry_date' => 'entryDate',
                'availability_date' => 'availabilityDate',
            ]);
        $tcaTransactionTable->create()
            ->setAllowed(true)
            ->setParentRequired(true)
            ->setFactory(new TransactionTcaCommandFactory('create'))
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

    /**
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @deprecated
     */
    private function unusedAssignments()
    {
        // define projection for the "BankDto" stream, containing
        // relations (see RelationalEvent) to Account streams
        ProjectionPool::provide()
            ->enrolProjection(
                '$' . static::STREAM_PREFIX_BANK
            )
            ->setProviderName(EntityProjectionProvider::class)
            // issue how the BankDto stream can continue with Account streams
            ->onStream(
                AssignedAccountNumberEvent::class,
                /**
                 * @param AssignedAccountEvent, $event
                 * @param EntityStreamProjection $projection
                 */
                function(AssignedAccountNumberEvent $event, EntityStreamProjection $projection)
                {
                    $event->cancel();
                    $projection->triggerProjection(
                        static::STREAM_PREFIX_ACCOUNT
                        . '/' . $event->getRelationId()->toString()
                    );
                }
            );

        // define projection for any "Account" streams (see the "*" wildcard
        // modifier used for the EventSelector)
        ProjectionPool::provide()
            ->enrolProjection(
                '$' . static::STREAM_PREFIX_ACCOUNT . '/*',
                '[' . AbstractAccountEvent::class . ']'
            )
            ->setProviderName(EntityProjectionProvider::class)
            ->setProviderOptions([
                'subjectName' => Account::class,
                'eventRepositoryName' => AccountEventRepository::class,
                'projectionRepositoryName' => AccountExtbaseRepository::class,
            ])
            // issue how any Account stream can continue with Transaction streams
            ->onStream(
                RelationalEvent::class,
                /**
                 * @param AttachedDepositTransactionEvent|AttachedDebitTransactionEvent $event
                 * @param EntityStreamProjection $projection
                 */
                function(RelationalEvent $event, EntityStreamProjection $projection)
                {
                    $projection->triggerProjection(
                        static::STREAM_PREFIX_TRANSACTION
                        . '/' . $event->getRelationId()->toString()
                    );
                }
            );

        // define projection for any "Transaction" streams (see the "*" wildcard
        // modifier used for the EventSelector)
        ProjectionPool::provide()
            ->enrolProjection(
                '$' . static::STREAM_PREFIX_TRANSACTION . '/*',
                '[' . AbstractTransactionEvent::class . ']'
            )
            ->setProviderName(EntityProjectionProvider::class)
            ->setProviderOptions([
                'subjectName' => AccountTransaction::class,
                'eventRepositoryName' => TransactionEventRepository::class,
                'projectionRepositoryName' => TransactionRepository::class,
            ]);
    }
}
