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

use H4ck3r31\BankAccountExample\Common;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Account;
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Iban\IbanProjectionRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\DatabaseFieldNameConverter;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * AccountProjectionRepository
 */
class AccountProjectionRepository
{
    const TABLE_NAME = 'tx_bankaccountexample_projection_account';

    /**
     * @return AccountProjectionRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AccountProjectionRepository::class);
    }

    /**
     * @param Iban $iban
     * @return Account|null
     */
    public function findByIban(Iban $iban)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->addIbanConstraint($queryBuilder, $iban);
        $data = $queryBuilder->setMaxResults(1)->execute()->fetch();

        return $this->buildOne($data);
    }

    /**
     * @param Bank $bank
     * @return Account[]
     */
    public function findByBank(Bank $bank)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->addBankConstraint($queryBuilder, $bank);
        $collection = $queryBuilder->execute()->fetchAll();

        return $this->buildMany($collection);
    }

    public function add(array $data)
    {
        $data = DatabaseFieldNameConverter::toDatabase($data);
        Common::getDatabaseConnection()
            ->insert(static::TABLE_NAME, $data);
    }

    public function update(string $iban, array $data)
    {
        $data = DatabaseFieldNameConverter::toDatabase($data);
        Common::getDatabaseConnection()
            ->update(static::TABLE_NAME, $data, ['iban' => $iban]);
    }

    /**
     * @param array|bool $data
     * @return Account|null
     */
    private function buildOne($data)
    {
        if (empty($data)) {
            return null;
        }

        $data = DatabaseFieldNameConverter::fromDatabase($data);
        return Account::buildFromProjection($data);
    }

    /**
     * @param array $collection
     * @return Account[]
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

    private function addBankConstraint(QueryBuilder $queryBuilder, Bank $bank)
    {
        $queryBuilder
            ->join(
                static::TABLE_NAME,
                IbanProjectionRepository::TABLE_NAME,
                IbanProjectionRepository::TABLE_NAME,
                $queryBuilder->expr()->eq(
                    static::TABLE_NAME . '.iban',
                    IbanProjectionRepository::TABLE_NAME . '.iban'
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    IbanProjectionRepository::TABLE_NAME . '.national_code',
                    $queryBuilder->createNamedParameter($bank->getNationalCode())
                ),
                $queryBuilder->expr()->eq(
                    IbanProjectionRepository::TABLE_NAME . '.branch_code',
                    $queryBuilder->createNamedParameter($bank->getBranchCode())
                ),
                $queryBuilder->expr()->eq(
                    IbanProjectionRepository::TABLE_NAME . '.subsidiary_code',
                    $queryBuilder->createNamedParameter($bank->getSubsidiaryCode())
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
            ->orderBy(static::TABLE_NAME . '.iban', 'ASC');
    }
}
