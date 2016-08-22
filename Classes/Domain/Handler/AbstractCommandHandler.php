<?php
namespace H4ck3r31\BankAccountExample\Domain\Handler;

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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Repository\EventRepository;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractEventEntity;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * AbstractCommandHandler
 */
abstract class AbstractCommandHandler
{
    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    protected static function createUuid()
    {
        return \Ramsey\Uuid\Uuid::uuid4();
    }

    /**
     * @var AbstractEvent[]
     */
    protected $events = [];

    /**
     * @var AbstractEntity
     */
    protected $subject;
}
