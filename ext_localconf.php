<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'H4ck3r31.BankAccountExample',
    'Management',
    [
        'Management' => 'listBanks, listAccounts, new, show, edit',
        'Command' => 'create, show, update, deposit, debit, close',
    ],
    [
        'Management' => 'listBanks, listAccounts, new, show, edit',
        'Command' => 'create, show, update, deposit, debit, close',
    ]
);

\H4ck3r31\BankAccountExample\Common::defineSettings();
\H4ck3r31\BankAccountExample\Common::registerEventSources();
\H4ck3r31\BankAccountExample\Common::registerTableCommands();
