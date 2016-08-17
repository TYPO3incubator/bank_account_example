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
    const NAME_STREAM_PREFIX = 'H4ck3r31.BankAccountExample-';

    public static function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    public static function registerEventSources()
    {
        ExtensionUtility::instance()
            ->addMapping(static::KEY_EXTENSION, 'tx_bankaccountexample_domain_model_account', Account::class)
            ->addMapping(static::KEY_EXTENSION, 'tx_bankaccountexample_domain_model_transaction', Transaction::class);

        SourceManager::provide()
            ->addSourcedTableName('tx_bankaccountexample_domain_model_account')
            ->addSourcedTableName('tx_bankaccountexample_domain_model_transaction');

        StreamProvider::provideFor(static::NAME_STREAM_PREFIX . 'Account')
            ->setEventNames([AbstractEvent::class])
            ->setStream(Stream::instance())
            ->setStore(EventStorePool::provide()->getDefault());
    }

    /**
     * @return StreamProvider
     */
    public static function getAccountStreamProvider()
    {
        return StreamProvider::provideFor(static::NAME_STREAM_PREFIX . 'Account');
    }
}
