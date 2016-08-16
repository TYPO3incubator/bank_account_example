<?php
return [
    'ctrl' => [
        'title'	=> 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction',
        'label' => 'entry_date',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,
        'versioningWS' => true,
        'versioning_followPages' => true,

        'delete' => 'deleted',
        'searchFields' => 'entry_date,availability_date,reference,value,',
        'iconfile' => 'EXT:bank_account_example/Resources/Public/Icons/tx_bankaccountexample_domain_model_transaction.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'entry_date, availability_date, reference, value',
    ],
    'types' => [
        '1' => ['showitem' => 'entry_date, availability_date, reference, value'],
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

        'entry_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.entry_date',
            'config' => [
                'dbType' => 'datetime',
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => '0000-00-00 00:00:00'
            ],

        ],
        'availability_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.availability_date',
            'config' => [
                'dbType' => 'datetime',
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => '0000-00-00 00:00:00'
            ],

        ],
        'reference' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.reference',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],

        ],
        'value' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.value',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]

        ],

        'bankaccount' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
