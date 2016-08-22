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
use H4ck3r31\BankAccountExample\EventSourcing\Saga;
use H4ck3r31\BankAccountExample\Domain\Transient\Bank;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;

/**
 * The repository for transient Bank
 * @deprecated
 */
class BankRepository
{
    /**
     * @return BankRepository
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(BankRepository::class);
    }

    /**
     * @return Bank
     */
    public function fetch()
    {
        $bank = Bank::instance();
        // Saga uses Stream with the common prefix,
        // that's why "Bank" is defined here.
        // This selects all events on Bank
        $desire = EventSelector::create('~Bank');
        Saga::create()->tell($bank, $desire);
        return $bank;
    }
}
