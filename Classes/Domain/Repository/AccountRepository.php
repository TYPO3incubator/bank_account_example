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
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Extbase\Persistence\ProjectionRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Accounts
 */
class AccountRepository extends Repository implements ProjectionRepository
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
        return parent::findAll();
    }

    /**
     * @param UuidInterface $uuid
     * @return null|Account
     */
    public function findByUuid(UuidInterface $uuid)
    {
        $query = $this->createQuery();

        return $query
            ->matching(
                $query->equals('uuid', $uuid->toString())
            )
            ->execute()
            ->getFirst();
    }

    /**
     * @param string $number
     * @return null|Account
     */
    public function findByNumber(string $number)
    {
        $query = $this->createQuery();

        return $query
            ->matching(
                $query->equals('number', $number)
            )
            ->execute()
            ->getFirst();
    }

    /**
     * @return null|Account
     */
    public function findByMaximumNumber()
    {
        return $this->createQuery()
            ->setOrderings(
                ['number' => QueryInterface::ORDER_DESCENDING]
            )
            ->setLimit(1)
            ->execute()
            ->getFirst();
    }
}
