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

use H4ck3r31\BankAccountExample\Domain\Event\AbstractEvent;
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Applicable\ApplicableAccount;
use H4ck3r31\BankAccountExample\Domain\Model\Applicable\ApplicableTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use H4ck3r31\BankAccountExample\EventSourcing\Stream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\EventSourcing\SourceManager;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventStorePool;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Stream\StreamProvider;
use TYPO3\CMS\DataHandling\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Common
 */
class Common
{
    const KEY_EXTENSION = 'bank_account_example';
    const NAME_COMMON_STREAM_PREFIX = 'H4ck3r31.BankAccountExample/';
    const NAME_ACCOUNT_STREAM_PREFIX = 'H4ck3r31.BankAccountExample/Account/';

    /**
     * Registers requirements for event sources processing with TYPO3.
     */
    public static function registerEventSources()
    {
        ExtensionUtility::instance()
            ->addMapping('tx_bankaccountexample_domain_model_account', Account::class)
            ->addMapping('tx_bankaccountexample_domain_model_account', ApplicableAccount::class)
            ->addMapping('tx_bankaccountexample_domain_model_transaction', Transaction::class)
            ->addMapping('tx_bankaccountexample_domain_model_transaction', ApplicableTransaction::class);

        SourceManager::provide()
            ->addSourcedTableName('tx_bankaccountexample_domain_model_account')
            ->addSourcedTableName('tx_bankaccountexample_domain_model_transaction');

        StreamProvider::provide()
            ->registerStream(
                static::NAME_ACCOUNT_STREAM_PREFIX,
                Stream::instance()->setPrefix(static::NAME_ACCOUNT_STREAM_PREFIX)
            );
    }

    /**
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
