<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account;

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

use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\DatabaseFieldNameConverter;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\Projection\TcaProjectionService;
use TYPO3\CMS\DataHandling\DataHandling\Infrastructure\Domain\Model\GenericEntity\UniversalProjectionRepository;

/**
 * Repository organizing TCA projections for Account
 */
class AccountTcaProjectionRepository extends UniversalProjectionRepository
{
    const TABLE_NAME = 'tx_bankaccountexample_domain_model_account';

    /**
     * @return AccountTcaProjectionRepository
     */
    public static function instance()
    {
        $repository = static::create(static::TABLE_NAME);
        $repository->forAll();
        return $repository;
    }

    /**
     * @param UuidInterface $aggregateId
     * @return mixed
     */
    public function findByAggregateId(UuidInterface $aggregateId)
    {
        return TcaProjectionService::findByUuid(
            static::TABLE_NAME,
            $aggregateId
        );
    }

    /**
     * @param array $data
     */
    public function add(array $data)
    {
        $data = TcaProjectionService::mapFieldNames(static::TABLE_NAME, $data);
        $data = TcaProjectionService::addCreateFieldValues(static::TABLE_NAME, $data);
        parent::add($data);
    }

    /**
     * @param string $identifier
     * @param array $data
     */
    public function update(string $identifier, array $data)
    {
        $data = DatabaseFieldNameConverter::toDatabase($data);
        $data = TcaProjectionService::mapFieldNames(static::TABLE_NAME, $data);
        $data = TcaProjectionService::addUpdateFieldValues(static::TABLE_NAME, $data);
        parent::update($identifier, $data);
    }
}
