<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Iban;

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

use H4ck3r31\BankAccountExample\Domain\Model\Iban\Event\AssignedAccountNumberEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventApplicable;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler\EventHandlerTrait;

/**
 * ExistingIban
 */
class ExistingIban implements EventApplicable
{
    use EventHandlerTrait;

    /**
     * @var Iban
     */
    private $iban;

    /**
     * @var Iban
     */
    private $existingIban;

    public function __construct(Iban $iban)
    {
        $this->iban = $iban;
    }

    /**
     * @return Iban
     */
    public function getExistingIban()
    {
        return $this->existingIban;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return !empty($this->existingIban);
    }

    /**
     * @param AssignedAccountNumberEvent $event
     */
    protected function onAssignedAccountNumberEvent(AssignedAccountNumberEvent $event)
    {
        if ((string)$event->getIban() === (string)$this->iban) {
            $this->existingIban = $event->getIban();
        }
    }
}
