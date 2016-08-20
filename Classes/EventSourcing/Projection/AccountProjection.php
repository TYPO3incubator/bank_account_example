<?php
namespace H4ck3r31\BankAccountExample\EventSourcing\Projection;

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
use H4ck3r31\BankAccountExample\Domain\Model\Applicable\ApplicableAccount;
use H4ck3r31\BankAccountExample\Domain\Repository\AccountRepository;
use H4ck3r31\BankAccountExample\EventSourcing\Saga;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Domain\Event\AbstractEvent;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Applicable;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;

/**
 * AccountProjection
 */
class AccountProjection extends AbstractProjection implements Applicable
{
    /**
     * @return AccountProjection
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountProjection::class);
    }

    public function __construct()
    {
        // fetch current UUIDs with accordant revisions
        $this->revisionReferences = AccountRepository::instance()->fetchRevisionReferences();
    }

    /**
     * Projects all accounts.
     */
    public function project()
    {
        // Saga uses Stream with the common prefix,
        // that's why "Bank" is defined here.
        // This selects all events on Bank
        $desire = EventSelector::create('~Bank');
        Saga::create()->tell($this, $desire);

        foreach ($this->revisionReferences as $revisionReference) {
            AccountRepository::instance()->removeByUuid(
                $revisionReference->getEntityReference()->getUuid()
            );
        }
    }

    public function apply(AbstractEvent $event)
    {
        if (!($event instanceof Event\CreatedAccountEvent)) {
            return;
        }

        $this->projectByUuid($event->getAggregateId());
    }

    public function projectByUuid(UuidInterface $uuid)
    {
        $account = $this->buildByUuid($uuid);

        // add/update if being forced or revisions are different
        if ($this->force || !$this->equalsRevisionReference($account) || true) {
            $revisionReference = $this->getRevisionReference($account->getUuid());

            if ($revisionReference === null) {
                AccountRepository::instance()->add($account);
                AccountRepository::instance()->persistAll();
            } else {
                $projectedAccount = AccountRepository::instance()->fetchByUuid($account->getUuid());
                $projectedAccount->_mergeProperties($account);
                AccountRepository::instance()->update($projectedAccount);
                AccountRepository::instance()->persistAll();
            }
        }

        $this->purgeRevisionReference($account->getUuid());
    }

    /**
     * @param UuidInterface $uuid
     * @return ApplicableAccount
     */
    public function buildByUuid(UuidInterface $uuid)
    {
        $account = ApplicableAccount::instance();
        // Saga uses Stream with the common prefix,
        // that's why "Account/" is defined here.
        // This selects all events on one Account for the given UUID
        $desire = EventSelector::create('~Account/' . $uuid->toString());
        Saga::create()->tell($account, $desire);
        return $account;
    }
}
