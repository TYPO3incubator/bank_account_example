<?php
namespace H4ck3r31\BankAccountExample\Domain\Model;

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

use H4ck3r31\BankAccountExample\Domain\Model\Account\AccountHolder;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\AbstractTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Common\Holdable;
use H4ck3r31\BankAccountExample\Domain\Model\Common\HoldableTrait;
use H4ck3r31\BankAccountExample\Domain\Model\Common\TransactionAttachable;
use H4ck3r31\BankAccountExample\Domain\Model\Common\TransactionAttachableTrait;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Event\StorableEvent;

/**
 * AbstractEvent
 */
abstract class AbstractEvent extends BaseEvent implements StorableEvent
{
    /**
     * @var Iban
     */
    protected $iban;

    /**
     * @return Iban
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        $data = [
            'iban' => (string)$this->getIban(),
        ];

        if ($this instanceof Holdable) {
            $data['accountHolder'] = $this->getAccountHolder()->getValue();
        }
        if ($this instanceof TransactionAttachable) {
            $data['transaction'] = $this->getTransaction()->toArray();
        }

        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    public function importData(array $data)
    {
        $this->iban = Iban::fromString($data['iban']);

        /** @var HoldableTrait $this */
        if ($this instanceof Holdable) {
            $this->accountHolder = AccountHolder::create($data['accountHolder']);
        }
        /** @var TransactionAttachableTrait $this */
        if ($this instanceof TransactionAttachable) {
            $this->transaction = AbstractTransaction::fromArray($data['transaction']);
        }
    }
}
