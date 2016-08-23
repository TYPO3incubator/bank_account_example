<?php
return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_account',
        'label' => 'holder',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,
        'versioningWS' => true,
        'versioning_followPages' => true,

        'searchFields' => 'holder,number,balance,transactions,',
        'iconfile' => 'EXT:bank_account_example/Resources/Public/Icons/tx_bankaccountexample_domain_model_account.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'closed, holder, number, balance, transactions',
    ],
    'types' => [
        '1' => ['showitem' => 'closed, holder, number, balance, transactions'],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],

        'closed' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_account.closed',
            'config' => [
                'type' => 'check',
            ],

        ],
        'holder' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_account.holder',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],

        ],
        'number' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_account.number',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],

        ],
        'balance' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_account.balance',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]

        ],
        'transactions' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_account.transactions',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_bankaccountexample_domain_model_transaction',
                'foreign_field' => 'account',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],

        ],
    ],
];
