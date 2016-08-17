<?php
namespace H4ck3r31\BankAccountExample\Domain\Projection;

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
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository;
use H4ck3r31\BankAccountExample\Domain\Saga\BankSaga;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Generic\EntityReference;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * AccountProjection
 */
class AccountProjection implements Applicable
{
    /**
     * @return AccountProjection
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountProjection::class);
    }

    /**
     * @var bool
     */
    protected $force = false;

    /**
     * @var EntityReference[]
     */
    protected $entityReferences;

    /**
     * @param bool $force
     * @return AccountProjection
     */
    public function setForce(bool $force)
    {
        $this->force = $force;
        return $this;
    }

    public function project()
    {
        // fetch current UUIDs with accordant revisions
        $this->entityReferences = AccountRepository::instance()->fetchEntityReferences();

        // process all account created events
        $epic = EventSelector::instance()
            ->setCategories([Common::NAME_STREAM_PREFIX . 'Bank']);
        BankSaga::create(Common::NAME_STREAM_PREFIX . 'Bank')
            ->tell($this, $epic);

        foreach ($this->entityReferences as $entityReference) {
            AccountRepository::instance()->removeByUuid($entityReference->getUuid());
        }
    }

    public function apply(AbstractEvent $event)
    {
        if (!($event instanceof Event\CreatedEvent)) {
            return;
        }

        $uuid = $event->getAccountId();
        $entityReference = $this->getEntityReference($uuid);

        // process the whole account events
        $account = Account::instance();
        $epic = EventSelector::instance()->setStreamName($uuid);
        BankSaga::create(Common::NAME_STREAM_PREFIX . 'Account')->tell($account, $epic);

        // add/update if being forced or revisions are different
        if ($this->force || !$this->equalsRevision($uuid, $account->getRevision())) {
            if ($entityReference !== null) {
                // @todo Get rid of Extbase's session magic when updating a projected record
                AccountRepository::instance()->removeByUuid($entityReference->getUuid());
            }
            AccountRepository::instance()->add($account);
        }

        $this->purgeEntityReference($uuid);
    }

    /**
     * @param string $uuid
     * @return EntityReference
     */
    protected function getEntityReference(string $uuid)
    {
        return ($this->entityReferences[$uuid] ?? null);
    }

    /**
     * @param string $uuid
     */
    protected function purgeEntityReference(string $uuid)
    {
        if ($this->getEntityReference($uuid) !== null) {
            unset($this->entityReferences[$uuid]);
        }
    }

    /**
     * @param string $uuid
     * @param int $revision
     * @return bool
     */
    protected function equalsRevision(string $uuid, int $revision)
    {
        $entityReference = $this->getEntityReference($uuid);
        return ($entityReference !== null && $entityReference->getRevision() === $revision);
    }

    /**
     * @return Session
     */
    protected function getPersistenceSession()
    {
        return Common::getObjectManager()->get(Session::class);
    }
}
