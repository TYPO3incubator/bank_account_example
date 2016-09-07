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
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Iban\IbanProjectionRepository;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\Projection;

/**
 * IbanProjection
 */
final class IbanProjection implements Projection
{
    /**
     * @return string[]
     */
    public function listensTo()
    {
        return [AssignedAccountNumberEvent::class];
    }

    /**
     * @param BaseEvent|AssignedAccountNumberEvent $event
     */
    public function project(BaseEvent $event)
    {
        IbanProjectionRepository::instance()->add(
            $event->getIban()->toArray()
        );
    }
}
