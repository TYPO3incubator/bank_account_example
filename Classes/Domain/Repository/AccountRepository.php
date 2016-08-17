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
use H4ck3r31\BankAccountExample\EventSourcing\AccountProjection;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Generic\EntityReference;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Generic\RevisionReference;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Accounts
 */
class AccountRepository extends Repository
{
    /**
     * @return AccountRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountRepository::class);
    }

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
        AccountProjection::instance()->project();
        return parent::findAll();
    }

    /**
     * @param string $number
     * @return null|Account
     */
    public function findByNumber(string $number)
    {
        // @todo Handle requirement of projection with a separate RevisionStore
        AccountProjection::instance()->project();
        $query = $this->createQuery();
        $query->matching($query->equals('number', $number));
        return $query->execute()->getFirst();
    }

    /**
     * @param string $uuid
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function removeByUuid(string $uuid)
    {
        $account = $this->fetchByUuid($uuid);
        if (!empty($account)) {
            $this->remove($account);
        }
    }

    /**
     * @param string $uuid
     * @return Account
     */
    public function fetchByUuid(string $uuid)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('uuid', $uuid));
        return $query->execute()->getFirst();
    }

    /**
     * @return RevisionReference[]
     */
    public function fetchRevisionReferences()
    {
        $revisionReferences = [];
        $query = $this->createQuery();
        foreach ($query->execute(true) as $account) {
            $reference = RevisionReference::fromRecord($this->getTableName(), $account);
            $revisionReferences[$reference->getEntityReference()->getUuid()] = $reference;;
        }
        return $revisionReferences;
    }

    /**
     * @param array $events
     */
    public function addEvents(array $events)
    {
        $streamProvider = Common::getAccountStreamProvider();

        foreach ($events as $event) {
            $streamProvider->commit($event, [Common::NAME_STREAM_PREFIX . 'Bank']);
        }
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->getDataMapper()->convertClassNameToTableName($this->objectType);
    }

    /**
     * @return DataMapper
     */
    protected function getDataMapper()
    {
        return Common::getObjectManager()->get(DataMapper::class);
    }
}
