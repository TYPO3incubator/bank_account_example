<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account;

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
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command\ChangeAccountHolderCommand;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command\CloseAccountCommand;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command\CreateAccountCommand;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command\DebitMoneyCommand;
use H4ck3r31\BankAccountExample\Domain\Model\Account\Command\DepositMoneyCommand;
use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DebitTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\DepositTransaction;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\Money;
use H4ck3r31\BankAccountExample\Domain\Model\Transaction\TransactionReference;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Account\AccountTcaProjectionRepository;
use H4ck3r31\BankAccountExample\Infrastructure\Domain\Model\Bank\NationalBankRepository;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command\AbstractCommand;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command\AttachRelationCommand;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command\ModifyEntityCommand;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command\CreateEntityBundleCommand;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command\DeleteEntityCommand;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command\ModifyEntityBundleCommand;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Aspect\Bundle;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Meta\EntityReference;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\TcaCommand\TcaCommand;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\TcaCommand\TcaCommandEntityBehavior;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Base\TcaCommand\TcaCommandFactory;

/**
 * AccountTcaCommandFactory
 */
final class AccountTcaCommandFactory implements TcaCommandFactory
{
    /**
     * @var array|\SplObjectStorage|AbstractCommand[]
     */
    private $createdCommands = [];

    /**
     * @var array|\SplObjectStorage|AbstractCommand[]
     */
    private $translatedCommands = [];

    /**
     * @var array|\SplObjectStorage|AbstractCommand[]
     */
    private $deferredCommands = [];

    public function __construct()
    {
        $this->createdCommands = new \SplObjectStorage();
        $this->translatedCommands = new \SplObjectStorage();
        $this->deferredCommands = new \SplObjectStorage();
    }

    public function process(AbstractCommand $command, TcaCommand $tcaCommand, TcaCommandEntityBehavior $entityBehavior)
    {
        if ($tcaCommand->getTableName() === Common::TCA_TABLE_NAME_ACCOUNT) {
            $this->processAccountCommand($command);
        } elseif ($tcaCommand->getTableName() === Common::TCA_TABLE_NAME_TRANSACTION) {
            $this->processTransactionCommand($command);
        }
    }

    public function getCreatedCommands(): \SplObjectStorage
    {
        return $this->createdCommands;
    }

    public function getTranslatedCommands(): \SplObjectStorage
    {
        return $this->translatedCommands;
    }

    private function processAccountCommand(AbstractCommand $command)
    {
        if ($command instanceof CreateEntityBundleCommand) {
            $data = $this->retrieveDataFromBundle($command);

            // skip IBAN check digits verification
            $iban = Iban::fromString($data['iban'] ?? null, false);
            $bank = NationalBankRepository::instance()
                ->findByNationalCode($iban->getNationalCode())
                ->findByBranchAndSubsidiaryCode($iban->getBranchCode(), $iban->getSubsidiaryCode());
            $accountHolder = AccountHolder::create($data['account_holder'] ?? null);

            $this->translatedCommands->attach($command);
            $this->createdCommands->attach(
                CreateAccountCommand::create(
                    $bank,
                    $accountHolder,
                    $iban->getAccountNumber()
                )
            );

            $this->processDeferredTransactions($command);
        }

        if ($command instanceof ModifyEntityBundleCommand) {
            $accountData = AccountTcaProjectionRepository::instance()
                ->findByAggregateId($command->getAggregateReference()->getUuidInterface());
            $data = $this->retrieveDataFromBundle($command);

            if (!empty($data['account_holder'])) {
                $iban = Iban::fromString($accountData['iban'] ?? null);
                $accountHolder = AccountHolder::create($data['account_holder']);

                $this->translatedCommands->attach($command);
                $this->createdCommands->attach(
                    ChangeAccountHolderCommand::create(
                        $iban,
                        $accountHolder
                    )
                );
            }

            $this->processDeferredTransactions($command);
        }

        if ($command instanceof DeleteEntityCommand) {
            $accountData = AccountTcaProjectionRepository::instance()
                ->findByAggregateId($command->getAggregateReference()->getUuidInterface());

            $iban = Iban::fromString($accountData['iban'] ?? null);

            $this->translatedCommands->attach($command);
            $this->createdCommands->attach(
                CloseAccountCommand::create($iban)
            );
        }
    }

    private function processTransactionCommand(AbstractCommand $command)
    {
        if (!($command instanceof CreateEntityBundleCommand)) {
            return;
        }

        $deferredCommand = $this->retrieveDeferredAccountCommand(
            $command->getAggregateReference()
        );
        if ($deferredCommand === null) {
            $this->deferredCommands->attach($command);
            return;
        }

        $this->attachTransaction($deferredCommand, $command);
    }

    private function processDeferredTransactions(Bundle $bundleCommand)
    {
        foreach ($bundleCommand->getCommands() as $command) {
            if (!($command instanceof AttachRelationCommand)) {
                continue;
            }

            $deferredCommand = $this->retrieveDeferredTransactionCommand(
                $command->getRelationReference()->getEntityReference()
            );
            // wait for transaction command if not deferred yet
            if ($deferredCommand === null) {
                $this->deferredCommands->attach($bundleCommand);
                continue;
            }

            if ($deferredCommand instanceof CreateEntityBundleCommand) {
                $this->attachTransaction($command, $deferredCommand);
            }
        }
    }

    private function attachTransaction(AttachRelationCommand $accountCommand, CreateEntityBundleCommand $transactionCommand)
    {
        $data = $this->retrieveDataFromBundle($transactionCommand);
        if (empty($data['type'])) {
            return;
        }

        $accountData = AccountTcaProjectionRepository::instance()
            ->findByAggregateId($accountCommand->getAggregateReference()->getUuidInterface());

        $iban = Iban::fromString($accountData['iban'] ?? null);
        $money = Money::create($data['money'] ?? null);
        $reference = TransactionReference::create($data['reference'] ?? '');
        $availabilityDate = new \DateTimeImmutable($data['availability_date'] ?? null);

        if ($data['type'] === DepositTransaction::class) {
            $this->deferredCommands->detach($transactionCommand);
            $this->createdCommands->attach(
                DepositMoneyCommand::create(
                    $iban,
                    $money,
                    $reference,
                    $availabilityDate
                )
            );
        } elseif ($data['type'] === DebitTransaction::class) {
            $this->deferredCommands->detach($transactionCommand);
            $this->createdCommands->attach(
                DebitMoneyCommand::create(
                    $iban,
                    $money,
                    $reference,
                    $availabilityDate
                )
            );
        }
    }

    /**
     * @param EntityReference $aggregateReference
     * @return null|AbstractCommand
     */
    private function retrieveDeferredTransactionCommand(EntityReference $aggregateReference)
    {
        foreach ($this->deferredCommands as $deferredCommand) {
            if (
                $deferredCommand instanceof CreateEntityBundleCommand
                && $aggregateReference->equals(
                    $deferredCommand->getAggregateReference()
                )
            ) {
                return $deferredCommand;
            }
        }
        return null;
    }

    private function retrieveDeferredAccountCommand(EntityReference $aggregateReference)
    {
        foreach ($this->deferredCommands as $deferredCommand) {
            if (
                $deferredCommand instanceof AttachRelationCommand
                && $aggregateReference->equals(
                    $deferredCommand->getRelationReference()->getEntityReference()
                )
            ) {
                return $deferredCommand;
            }
        }
        return null;
    }

    /**
     * @param Bundle $bundleCommand
     * @return array
     */
    private function retrieveDataFromBundle(Bundle $bundleCommand)
    {
        $data = [];
        foreach ($bundleCommand->getCommands() as $command) {
            if ($command instanceof ModifyEntityCommand) {
                $data = array_merge($data, $command->getData());
            }
        }
        return $data;
    }
}
