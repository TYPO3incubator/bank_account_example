<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'H4ck3r31.BankAccountExample',
    'Management',
    ['Account' => 'list, new, create, show, edit, update, close'],
    ['Account' => 'list, new, create, show, edit, update, close']
);

\H4ck3r31\BankAccountExample\Common::registerEventSources();
