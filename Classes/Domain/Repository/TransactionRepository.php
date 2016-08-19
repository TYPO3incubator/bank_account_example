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
use H4ck3r31\BankAccountExample\Domain\Model\Transaction;
use H4ck3r31\BankAccountExample\EventSourcing\Projection\TransactionProjection;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Extbase\Persistence\EventRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;

/**
 * The repository for Transaction
 */
class TransactionRepository extends EventRepository
{
    /**
     * @return TransactionRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(TransactionRepository::class);
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
     * @param UuidInterface $uuid
     * @return null|Transaction
     */
    public function findByUuid(UuidInterface $uuid)
    {
        TransactionProjection::instance()->projectByUuid($uuid);
        return $this->fetchByUuid($uuid);
    }

    /**
     * @param UuidInterface $uuid
     */
    public function projectByUid(UuidInterface $uuid)
    {
        TransactionProjection::instance()->projectByUuid($uuid);
    }

    /**
     * @param UuidInterface $uuid
     * @return Transaction
     */
    public function buildByUuid(UuidInterface $uuid)
    {
        return TransactionProjection::instance()->buildByUuid($uuid);
    }
}
