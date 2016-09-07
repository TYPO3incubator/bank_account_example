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
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\AbstractTransaction;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\DatabaseFieldNameConverter;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Repository organizing projections for Transaction
 */
class TransactionProjectionRepository
{
    const TABLE_NAME = 'tx_bankaccountexample_projection_transaction';

    /**
     * @return TransactionProjectionRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(static::class);
    }

    /**
     * @param UuidInterface $transactionId
     * @return AbstractTransaction|null
     */
    public function findByTransactionId(UuidInterface $transactionId)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->addTransactionIdConstraint($queryBuilder, $transactionId);
        $data = $queryBuilder->setMaxResults(1)->execute()->fetch();

        return $this->buildOne($data);
    }

    /**
     * @param Iban $iban
     * @return AbstractTransaction[]
     */
    public function findByIban(Iban $iban)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->addIbanConstraint($queryBuilder, $iban);
        $collection = $queryBuilder->execute()->fetchAll();

        return $this->buildMany($collection);
    }

    public function add(array $data)
    {
        $data = DatabaseFieldNameConverter::toDatabase($data);
        Common::getDatabaseConnection()
            ->insert(static::TABLE_NAME, $data);
    }

    public function update(string $transactionId, array $data)
    {
        $data = DatabaseFieldNameConverter::toDatabase($data);
        Common::getDatabaseConnection()
            ->update(static::TABLE_NAME, $data, ['transaction_id' => $transactionId]);
    }

    /**
     * @param array|bool $data
     * @return AbstractTransaction|null
     */
    private function buildOne($data)
    {
        if (empty($data)) {
            return null;
        }

        $data = DatabaseFieldNameConverter::fromDatabase($data);
        return AbstractTransaction::buildFromProjection($data);
    }

    /**
     * @param array $collection
     * @return AbstractTransaction[]
     */
    private function buildMany(array $collection)
    {
        $accounts = [];
        foreach ($collection as $data) {
            if (empty($data)) {
                continue;
            }
            $accounts[] = $this->buildOne($data);
        }
        return $accounts;
    }

    private function addTransactionIdConstraint(QueryBuilder $queryBuilder, UuidInterface $transactionId)
    {
        $queryBuilder
            ->where(
                $queryBuilder->expr()->eq(
                    'transaction_id',
                    $queryBuilder->createNamedParameter($transactionId->toString())
                )
            );
    }

    private function addIbanConstraint(QueryBuilder $queryBuilder, Iban $iban)
    {
        $queryBuilder
            ->where(
                $queryBuilder->expr()->eq(
                    'iban',
                    $queryBuilder->createNamedParameter((string)$iban)
                )
            );
    }

    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder()
    {
        return Common::getDatabaseConnection()->createQueryBuilder()
            ->select('*')
            ->from(static::TABLE_NAME)
            ->orderBy('entry_date', 'DESC');
    }
}
