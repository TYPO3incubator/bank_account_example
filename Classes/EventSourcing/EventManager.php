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

use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Handler\AccountEventHandler;
use H4ck3r31\BankAccountExample\Domain\Handler\TransactionEventHandler;
use H4ck3r31\BankAccountExample\Domain\Repository\RepositoryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractEventEntity;
use TYPO3\CMS\Extbase\Persistence\Repository;

class EventManager implements Instantiable
{
    /**
     * @return EventManager
     */
    static public function instance()
    {
        return GeneralUtility::makeInstance(EventManager::class);
    }

    protected $eventTargets = [];

    protected $targetRepositories = [];

    protected $eventHandlers = [];

    protected $eventInstantiators = [];

    /**
     * @param AbstractEvent $event
     * @return null|void
     */
    public function manage(AbstractEvent $event)
    {
        $this->initialize();

        $repository = $this->provideRepository($event);
        if ($repository === null) {
            return null;
        }

        $target = $this->provideTarget($event, $repository);
        if ($target === null) {
            return null;
        }

        $handler = $this->provideEventHandler($event, $target);
        if ($handler === null) {
            return;
        }

        $handler->apply($event);

        if ($target->_isNew()) {
            $repository->add($target);
        } else {
            $repository->update($target);
        }
    }

    /**
     * @param AbstractEvent $event
     * @param AbstractEventEntity $target
     * @return \TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable
     */
    protected function provideEventHandler(AbstractEvent $event, AbstractEventEntity $target)
    {
        foreach ($this->eventHandlers as $eventHandlerClassName => $eventNames) {
            foreach ($eventNames as $eventName) {
                if (is_a($event, $eventName)) {
                    /** @var Instantiable $eventHandlerClassName */
                    return $eventHandlerClassName::create($target);
                }
            }
        }
        return null;
    }

    /**
     * @param AbstractEvent|Event\AbstractEvent $event
     * @param Repository $repository
     * @return null|AbstractEventEntity
     */
    protected function provideTarget(AbstractEvent $event, RepositoryInterface $repository)
    {
        $targetClassName = $this->provideTargetClassName($event);
        if ($targetClassName === null) {
            return null;
        }

        if (in_array(get_class($event), $this->eventInstantiators)) {
            $target = $targetClassName::instance();
        } else {
            $target = $repository->findByUuid($event->getAggregateId());
        }

        return $target;
    }

    /**
     * @param AbstractEvent $event
     * @return null|string|Instantiable
     */
    protected function provideTargetClassName(AbstractEvent $event)
    {
        foreach ($this->eventTargets as $targetClassName => $eventNames) {
            foreach ($eventNames as $eventName) {
                if (is_a($event, $eventName)) {
                    return $targetClassName;
                }
            }
        }
        return null;
    }

    /**
     * @param AbstractEvent $event
     * @return null
     */
    protected function provideRepository(AbstractEvent $event)
    {
        $targetClassName = $this->provideTargetClassName($event);
        if ($targetClassName === null) {
            return null;
        }

        if (empty($this->targetRepositories[$targetClassName])) {
            return null;
        }

        /** @var RepositoryInterface $repositoryClassName */
        $repositoryClassName = $this->targetRepositories[$targetClassName];

        return $repositoryClassName::instance();
    }

    protected function initialize()
    {
        $this->eventTargets = [
            \H4ck3r31\BankAccountExample\Domain\Model\Account::class => [
                Event\AbstractAccountEvent::class,
            ],
            \H4ck3r31\BankAccountExample\Domain\Model\Transaction::class => [
                Event\AbstractTransactionEvent::class,
            ],
        ];
        $this->targetRepositories = [
            \H4ck3r31\BankAccountExample\Domain\Model\Account::class
                => \H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository::class,
            \H4ck3r31\BankAccountExample\Domain\Model\Transaction::class
                => \H4ck3r31\BankAccountExample\Domain\Repository\TransactionRepository::class,
        ];
        $this->eventHandlers = [
            AccountEventHandler::class => [
                Event\AbstractAccountEvent::class,
            ],
            TransactionEventHandler::class => [
                Event\AbstractTransactionEvent::class,
            ],
        ];
        $this->eventInstantiators = [
            Event\CreatedAccountEvent::class,
            Event\CreatedTransactionEvent::class,
        ];
    }
}
