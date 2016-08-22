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

use H4ck3r31\BankAccountExample\Common;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

/**
 * AssignedAccountEvent
 */
class AssignedAccountEvent extends AbstractEvent implements Instantiable
{
    /**
     * @return AssignedAccountEvent
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AssignedAccountEvent::class);
    }

    /**
     * @param UuidInterface $accountId
     * @return AssignedAccountEvent
     */
    public static function create(UuidInterface $accountId)
    {
        $event = static::instance();
        $event->accountId = $accountId;
        return $event;
    }

    /**
     * @var UuidInterface
     */
    protected $accountId;

    /**
     * @return UuidInterface
     */
    public function getAccountId(): UuidInterface
    {
        return $this->accountId;
    }
}
