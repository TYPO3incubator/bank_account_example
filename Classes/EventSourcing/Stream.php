<?php
namespace H4ck3r31\BankAccountExample\EventSourcing;

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
use H4ck3r31\BankAccountExample\Domain\Object\EventException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Stream\AbstractStream;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;

class Stream extends AbstractStream implements Instantiable
{
    /**
     * @return Stream
     */
    static public function instance()
    {
        return GeneralUtility::makeInstance(Stream::class);
    }

    /**
     * @var string
     */
    protected $prefix = Common::NAME_COMMON_STREAM_PREFIX;

    /**
     * @param AbstractEvent $event
     * @return string
     * @throws EventException
     */
    protected function determineStreamNameByEvent(AbstractEvent $event): string
    {
        if (!($event instanceof \H4ck3r31\BankAccountExample\Domain\Event\AbstractEvent)) {
            throw new EventException('Received invalid event type', 1471431286);
        }

        return $this->prefix($event->getAggregateId());
    }
}
