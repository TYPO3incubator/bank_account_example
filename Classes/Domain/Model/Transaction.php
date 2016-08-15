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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Transaction
 */
class Transaction extends AbstractEntity
{
    /**
     * @var \DateTime
     */
    protected $entryDate = null;

    /**
     * @var \DateTime
     */
    protected $availabilityDate = null;

    /**
     * @var string
     */
    protected $reference = '';

    /**
     * @var double
     */
    protected $value = 0.0;

    /**
     * @return \DateTime $entryDate
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * @param \DateTime $entryDate
     */
    public function setEntryDate(\DateTime $entryDate)
    {
        $this->entryDate = $entryDate;
    }

    /**
     * @return \DateTime $availabilityDate
     */
    public function getAvailabilityDate()
    {
        return $this->availabilityDate;
    }

    /**
     * @param \DateTime $availabilityDate
     */
    public function setAvailabilityDate(\DateTime $availabilityDate)
    {
        $this->availabilityDate = $availabilityDate;
    }

    /**
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return double $value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param double $value
     */
    public function setValue(double $value)
    {
        $this->value = $value;
    }
}
