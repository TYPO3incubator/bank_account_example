<?php
namespace H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Iban;

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
use H4ck3r31\BankAccountExample\Domain\Model\Bank\Bank;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\AccountNumber;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\DatabaseFieldNameConverter;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\EventSourcing\Infrastructure\Domain\Model\Base\ProjectionRepository;

/**
 * Repository organizing projections for Account
 */
class IbanProjectionRepository implements ProjectionRepository
{
    const TABLE_NAME = 'tx_bankaccountexample_projection_iban';

    /**
     * @return IbanProjectionRepository
     */
    public static function instance()
    {
        return new static();
    }

    /**
     * @param Iban $iban
     * @return Iban|null
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
     * @return Iban[]
     */
    public function findByBank(Bank $bank)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->addBankConstraint($queryBuilder, $bank);
        $collection = $queryBuilder->execute()->fetchAll();

        return $this->buildMany($collection);
    }

    /**
     * @param Bank $bank
     * @param AccountNumber $accountNumber
     * @return Iban|null
     */
    public function findByBankAndAccountNumber(Bank $bank, AccountNumber $accountNumber)
    {
        $queryBuilder = $this->createQueryBuilder();
        $this->addBankConstraint($queryBuilder, $bank);
        $this->addAccountNumberConstraint($queryBuilder, $accountNumber);
        $data = $queryBuilder->setMaxResults(1)->execute()->fetch();

        return $this->buildOne($data);
    }

    public function add(array $data)
    {
        $data = DatabaseFieldNameConverter::toDatabase($data);
        Common::getDatabaseConnection()->insert(static::TABLE_NAME, $data);
    }

    public function update(string $identifier, array $data)
    {
        throw new \RuntimeException('Updating IBAN projections is denied');
    }

    /**
     * @param array|bool $data
     * @return Iban|null
     */
    private function buildOne($data)
    {
        if (empty($data)) {
            return null;
        }

        return Iban::fromString($data['iban']);
    }

    /**
     * @param array $collection
     * @return Iban[]
     */
    private function buildMany(array $collection)
    {
        $ibans = [];
        foreach ($collection as $data) {
            if (empty($data)) {
                continue;
            }
            $ibans[] = $this->buildOne($data);
        }
        return $ibans;
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
            ->where(
                $queryBuilder->expr()->eq(
                    'national_code',
                    $queryBuilder->createNamedParameter($bank->getNationalCode())
                ),
                $queryBuilder->expr()->eq(
                    'branch_code',
                    $queryBuilder->createNamedParameter($bank->getBranchCode())
                ),
                $queryBuilder->expr()->eq(
                    'subsidiary_code',
                    $queryBuilder->createNamedParameter($bank->getSubsidiaryCode())
                )
            );
    }

    private function addAccountNumberConstraint(QueryBuilder $queryBuilder, AccountNumber $accountNumber)
    {
        $queryBuilder
            ->where(
                $queryBuilder->expr()->eq(
                    'account_number',
                    $queryBuilder->createNamedParameter((string)$accountNumber)
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
            ->from(static::TABLE_NAME);
    }
}
