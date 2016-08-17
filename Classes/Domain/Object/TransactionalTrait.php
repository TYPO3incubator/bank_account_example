<?php
namespace H4ck3r31\BankAccountExample\Domain\Object;

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

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * TransactionalTrait
 */
trait TransactionalTrait
{
    /**
     * @var double
     */
    protected $value;

    /**
     * @var \DateTime
     */
    protected $entryDate;

    /**
     * @var \DateTime
     */
    protected $availabilityDate;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @return double
     */
    public function getValue(): double
    {
        return $this->value;
    }

    /**
     * @return \DateTime
     */
    public function getEntryDate(): \DateTime
    {
        return $this->entryDate;
    }

    /**
     * @return \DateTime
     */
    public function getAvailabilityDate(): \DateTime
    {
        return $this->availabilityDate;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return array
     */
    public function transactionalToArray(): array
    {
        return [
            'value' => $this->value,
            'entryDate' => $this->entryDate,
            'availabilityDate' => $this->availabilityDate,
            'reference' => $this->reference,
        ];
    }

    public function transactionalFromArray(array $data)
    {

    }
}
