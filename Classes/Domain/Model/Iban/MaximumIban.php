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
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Infrastructure\Service\NationalBank\CoreDataFactory;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\Event\EventApplicable;
use TYPO3\CMS\EventSourcing\Core\Domain\Model\Base\Event\EventHandlerTrait;

/**
 * MaximumIban
 */
class MaximumIban implements EventApplicable
{
    use EventHandlerTrait;

    /**
     * @var Iban
     */
    private $maximumIban;

    /**
     * @var Bank
     */
    private $bank;

    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return Iban
     */
    public function getMaximumIban()
    {
        return $this->maximumIban;
    }

    /**
     * @return Iban
     */
    public function incrementAccountNumber()
    {
        if (empty($this->maximumIban)) {
            $accountNumber = 1;
        } else {
            $accountNumber = (int)$this->maximumIban->getAccountNumber()->__toString() + 1;
        }

        $coreData = CoreDataFactory::createFor($this->bank->getNationalCode());
        $accountNumber = $coreData->convertAccountNumber($accountNumber);

        return $this->bank->compileIban($accountNumber);
    }

    /**
     * @param AssignedAccountNumberEvent $event
     */
    protected function applyAssignedAccountNumberEvent(AssignedAccountNumberEvent $event)
    {
        if (!$event->getIban()->belongsTo($this->bank)) {
            return;
        }

        if (empty($this->maximumIban)) {
            $this->maximumIban = $event->getIban();
            return;
        }

        $compare = strcmp(
            (string)$event->getIban()->getAccountNumber(),
            (string)$this->maximumIban->getAccountNumber()
        );
        if ($compare === 1) {
            $this->maximumIban = $event->getIban();
        }
    }
}
