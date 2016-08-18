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
     * @param string $name
     * @return Stream
     */
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @param AbstractEvent $event
     * @return Stream
     */
    public function publish(AbstractEvent $event)
    {
        if (!($event instanceof \H4ck3r31\BankAccountExample\Domain\Event\AbstractEvent)) {
            throw new \RuntimeException('Recieved invalid event type', 1471431285);
        }

        foreach ($this->consumers as $consumer) {
            call_user_func($consumer, $event);
        }

        return $this;
    }

    /**
     * @param callable $consumer
     * @return Stream
     */
    public function subscribe(callable $consumer)
    {
        if (!in_array($consumer, $this->consumers, true)) {
            $this->consumers[] = $consumer;
        }
        return $this;
    }

    /**
     * @param AbstractEvent $event
     * @return string
     */
    public function determineNameByEvent(AbstractEvent $event): string
    {
        if (!($event instanceof \H4ck3r31\BankAccountExample\Domain\Event\AbstractEvent)) {
            throw new \RuntimeException('Recieved invalid event type', 1471431286);
        }

        return $this->prefix($event->getAggregateId());
    }
}
