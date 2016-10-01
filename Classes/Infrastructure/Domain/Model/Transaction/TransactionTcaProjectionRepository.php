<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Transaction;

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
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Repository\ProjectionRepository;
use TYPO3\CMS\DataHandling\Core\Framework\Process\Projection\TcaProjectionService;

/**
 * Repository organizing TCA projections for Transaction
 */
class TransactionTcaProjectionRepository implements ProjectionRepository
{
    const TABLE_NAME = 'tx_bankaccountexample_domain_model_transaction';

    /**
     * @return TransactionTcaProjectionRepository
     */
    public static function instance()
    {
        return new static();
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

    public function add(array $data)
    {
        $data = TcaProjectionService::mapFieldNames(static::TABLE_NAME, $data);
        $data = TcaProjectionService::addCreateFieldValues(static::TABLE_NAME, $data);
        Common::getDatabaseConnection()
            ->insert(static::TABLE_NAME, $data);
    }

    public function update(string $identifier, array $data)
    {
        throw new \RuntimeException('Updating Transaction projections is denied');
    }
}
