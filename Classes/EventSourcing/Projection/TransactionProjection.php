<?php
namespace H4ck3r31\BankAccountExample\EventSourcing\Projection;

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
use H4ck3r31\BankAccountExample\Domain\Event;
use H4ck3r31\BankAccountExample\Domain\Model\Applicable\ApplicableTransaction;
use H4ck3r31\BankAccountExample\Domain\Repository\TransactionRepository;
use H4ck3r31\BankAccountExample\EventSourcing\Saga;
use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;

/**
 * TransactionProjection
 */
class TransactionProjection extends AbstractProjection
{
    /**
     * @return TransactionProjection
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(TransactionProjection::class);
    }

    public function __construct()
    {
        // fetch current UUIDs with accordant revisions
        $this->revisionReferences = TransactionRepository::instance()->fetchRevisionReferences();
    }

    public function projectByUuid(UuidInterface $uuid)
    {
        $transaction = $this->buildByUuid($uuid);

        // add/update if being forced or revisions are different
        if ($this->force || !$this->equalsRevisionReference($transaction)) {
            $revisionReference = $this->getRevisionReference($transaction->getUuid());

            if ($revisionReference === null) {
                TransactionRepository::instance()->add($transaction);
                TransactionRepository::instance()->persistAll();
            } else {
                $projectedTransaction = TransactionRepository::instance()->fetchByUuid($transaction->getUuid());
                $projectedTransaction->_mergeProperties($transaction);
                TransactionRepository::instance()->update($projectedTransaction);
                TransactionRepository::instance()->persistAll();
            }
        }

        $this->purgeRevisionReference($transaction->getUuid());
    }

    /**
     * @param UuidInterface $uuid
     * @return ApplicableTransaction
     */
    public function buildByUuid(UuidInterface $uuid)
    {
        $transaction = ApplicableTransaction::instance();
        $epic = EventSelector::instance()->setStreamName($uuid);
        Saga::create(Common::NAME_STREAM_PREFIX . 'Account')->tell($transaction, $epic);
        return $transaction;
    }
}
