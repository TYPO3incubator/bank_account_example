<?php
return [
    'ctrl' => [
        'type' => 'type',
        'title'	=> 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction',
        'label' => 'entry_date',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => 1,
        'versioningWS' => true,
        'versioning_followPages' => true,
        'eventSourcing' => [
            'listenEvents' => true,
            'recordEvents' => false,
            'projectEvents' => false,
        ],

        'searchFields' => 'transaction_id,entry_date,availability_date,reference,money,',
        'iconfile' => 'EXT:bank_account_example/Resources/Public/Icons/tx_bankaccountexample_domain_model_transaction.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'type, transaction_id, entry_date, availability_date, reference, money',
    ],
    'types' => [
        '1' => ['showitem' => 'type, transaction_id, entry_date, availability_date, reference, money'],
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

        'transaction_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.transaction_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim,required',
            ]
        ],

        'type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.type',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.type.none',
                        '',
                    ],
                    [
                        'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.type.deposit',
                        \H4ck3r31\BankAccountExample\Domain\Model\Transaction\DepositTransaction::class,
                    ],
                    [
                        'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.type.debit',
                        \H4ck3r31\BankAccountExample\Domain\Model\Transaction\DebitTransaction::class,
                    ],
                ],
                'eval' => 'required',
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
        'money' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bank_account_example/Resources/Private/Language/locallang_db.xlf:tx_bankaccountexample_domain_model_transaction.money',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2,required',
                'default' => '0.00',
            ]
        ],

        'account' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
