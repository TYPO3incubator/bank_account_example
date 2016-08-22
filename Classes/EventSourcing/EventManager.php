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
use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Handler\AccountEventHandler;
use H4ck3r31\BankAccountExample\Domain\Handler\TransactionEventHandler;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Event\Definition\EntityEvent;
use TYPO3\CMS\DataHandling\Core\Object\Instantiable;
use TYPO3\CMS\DataHandling\Extbase\DomainObject\AbstractProjectableEntity;
use TYPO3\CMS\DataHandling\Extbase\Persistence\RepositoryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class EventManager implements Instantiable
{
    /**
     * @return EventManager
     */
    static public function instance()
    {
        return Common::getObjectManager()->get(EventManager::class);
    }

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    protected $eventTargets = [];

    protected $targetRepositories = [];

    protected $eventHandlers = [];

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager
     */
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

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

        $this->persistenceManager->persistAll();
    }

    /**
     * @param AbstractEvent $event
     * @param AbstractProjectableEntity $target
     * @return \TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable
     */
    protected function provideEventHandler(AbstractEvent $event, AbstractProjectableEntity $target)
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
     * @return null|AbstractProjectableEntity
     */
    protected function provideTarget(AbstractEvent $event, RepositoryInterface $repository)
    {
        $targetClassName = $this->provideTargetClassName($event);
        if ($targetClassName === null) {
            return null;
        }

        if ($event instanceof EntityEvent) {
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
     * @return Repository|RepositoryInterface
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
    }
}
