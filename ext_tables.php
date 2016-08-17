<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'BankAccountExample'
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'H4ck3r31.BankAccountExample',
    'Management',
    'Bank Account Management'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_bankaccountexample_domain_model_account'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
    'tx_bankaccountexample_domain_model_transaction'
);
