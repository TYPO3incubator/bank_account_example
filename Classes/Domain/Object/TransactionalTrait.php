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

/**
 * TransactionalTrait
 */
trait TransactionalTrait
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var \DateTime
     */
    protected $entryDate;

    /**
     * @var \DateTime
     */
    protected $availabilityDate;

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return \DateTime
     */
    public function getEntryDate(): \DateTime
    {
        return $this->entryDate;
    }

    /**
     * @return null|\DateTime
     */
    public function getAvailabilityDate()
    {
        return $this->availabilityDate;
    }
}
