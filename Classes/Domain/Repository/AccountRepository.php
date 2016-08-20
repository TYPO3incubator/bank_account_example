<?php
namespace H4ck3r31\BankAccountExample\Domain\Repository;

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
use H4ck3r31\BankAccountExample\Domain\Model\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Applicable\ApplicableAccount;
use H4ck3r31\BankAccountExample\EventSourcing\Projection\AccountProjection;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Stream\StreamProvider;
use TYPO3\CMS\DataHandling\Extbase\Persistence\EventRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The repository for Accounts
 */
class AccountRepository extends EventRepository
{
    /**
     * @return AccountRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountRepository::class);
    }

    /**
     * @param QuerySettingsInterface $defaultQuerySettings
     */
    public function injectDefaultQuerySettings(QuerySettingsInterface $defaultQuerySettings)
    {
        $this->defaultQuerySettings = $defaultQuerySettings;
        $this->defaultQuerySettings->setRespectStoragePage(false);
    }

    /**
     * @return array|QueryResultInterface|Account[]
     */
    public function findAll()
    {
        // @todo Handle requirement of projection with a separate RevisionStore
        $this->buildAll();
        return parent::findAll();
    }

    /**
     * @param UuidInterface $uuid
     * @return null|Account
     */
    public function findByUuid(UuidInterface $uuid)
    {
        $this->projectByUuid($uuid);
        return $this->fetchByUuid($uuid);
    }

    /**
     * @param string $number
     * @return null|Account
     */
    public function findByNumber(string $number)
    {
        // @todo Handle requirement of projection with a separate RevisionStore
        $this->buildAll();
        $query = $this->createQuery();
        $query->matching($query->equals('number', $number));
        return $query->execute()->getFirst();
    }

    public function buildAll()
    {
        AccountProjection::instance()->project();
    }

    public function projectByUuid(UuidInterface $uuid)
    {
        AccountProjection::instance()->projectByUuid($uuid);
    }

    /**
     * @param UuidInterface $uuid
     * @return ApplicableAccount
     */
    public function buildByUuid(UuidInterface $uuid)
    {
        return AccountProjection::instance()->buildByUuid($uuid);
    }

    /**
     * @param array $events
     */
    public function addEvents(array $events)
    {
        $stream = StreamProvider::provide()->useStream(Common::NAME_ACCOUNT_STREAM_PREFIX);

        foreach ($events as $event) {
            $stream->commit($event);
        }
    }
}
