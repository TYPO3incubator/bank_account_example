<?php
namespace H4ck3r31\BankAccountExample\Domain\Event;

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

use H4ck3r31\BankAccountExample\Domain\Object\Transactional;
use H4ck3r31\BankAccountExample\Domain\Object\TransactionalTrait;

/**
 * AbstractTransactionEvent
 */
abstract class AbstractTransactionEvent extends AbstractEvent implements Transactional
{
    use TransactionalTrait;

    /**
     * @return array
     */
    public function exportData()
    {
        $data = parent::exportData();

        $data['value'] = $this->value;
        $data['reference'] = $this->reference;
        $data['entryDate'] = $this->entryDate->format(\DateTime::W3C);
        $data['availabilityDate'] = $this->availabilityDate->format(\DateTime::W3C);

        return $data;
    }

    /**
     * @param array|null $data
     * @return void
     */
    public function importData($data)
    {
        parent::importData($data);

        $this->value = $data['value'];
        $this->reference = $data['reference'];
        $this->entryDate = new \DateTime($data['entryDate']);
        $this->availabilityDate = new \DateTime($data['availabilityDate']);
    }
}
