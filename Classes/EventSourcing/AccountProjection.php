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
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Generic\RevisionReference;
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
     * @var RevisionReference[]
     */
    protected $revisionReferences;

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
        $this->revisionReferences = AccountRepository::instance()->fetchRevisionReferences();

        // process all account created events
        $epic = EventSelector::instance()
            ->setCategories([Common::NAME_STREAM_PREFIX . 'Bank']);
        Saga::create(Common::NAME_STREAM_PREFIX . 'Bank')
            ->tell($this, $epic);

        foreach ($this->revisionReferences as $revisionReference) {
            AccountRepository::instance()->removeByUuid($revisionReference->getEntityReference()->getUuid());
        }
    }

    /**
     * @param UuidInterface $uuid
     * @return Account
     */
    public function buildByUuid(UuidInterface $uuid)
    {
        $account = Account::instance();
        $epic = EventSelector::instance()->setStreamName($uuid);
        Saga::create(Common::NAME_STREAM_PREFIX . 'Account')->tell($account, $epic);
        return $account;
    }

    public function apply(AbstractEvent $event)
    {
        if (!($event instanceof Event\CreatedAccountEvent)) {
            return;
        }

        $uuid = $event->getAggregateId();
        $revisionReference = $this->getRevisionReference($uuid);

        // process the whole account events
        $account = $this->buildByUuid($uuid);

        // add/update if being forced or revisions are different
        if ($this->force || !$this->equalsRevision($uuid, $account->getRevision())) {
            if ($revisionReference !== null) {
                // @todo Get rid of Extbase's session magic when updating a projected record
                AccountRepository::instance()->removeByUuid($revisionReference->getEntityReference()->getUuid());
            }
            AccountRepository::instance()->add($account);
        }

        $this->purgeRevisionReference($uuid);
    }

    /**
     * @param string $uuid
     * @return RevisionReference
     */
    protected function getRevisionReference(string $uuid)
    {
        return ($this->revisionReferences[$uuid] ?? null);
    }

    /**
     * @param string $uuid
     */
    protected function purgeRevisionReference(string $uuid)
    {
        if ($this->getRevisionReference($uuid) !== null) {
            unset($this->revisionReferences[$uuid]);
        }
    }

    /**
     * @param string $uuid
     * @param int $revision
     * @return bool
     */
    protected function equalsRevision(string $uuid, int $revision)
    {
        $revisionReference = $this->getRevisionReference($uuid);
        return ($revisionReference !== null && $revisionReference->getRevision() === $revision);
    }

    /**
     * @return Session
     */
    protected function getPersistenceSession()
    {
        return Common::getObjectManager()->get(Session::class);
    }
}
