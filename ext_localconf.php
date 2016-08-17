<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'H4ck3r31.BankAccountExample',
    'Management',
    ['Account' => 'list, new, create, edit, update, delete'],
    ['Account' => 'list, new, create, edit, update, delete']
);
