<?php
namespace H4ck3r31\BankAccountExample\EventSourcing;

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
use TYPO3\CMS\DataHandling\Core\EventSourcing\Saga\AbstractSaga;

/**
 * Saga
 */
class Saga extends AbstractSaga
{
    /**
     * @param string $name
     * @return Saga
     */
    public static function create(string $name)
    {
        return Common::getObjectManager()->get(Saga::class, $name);
    }

    protected function getStream()
    {
        return Stream::instance();
    }
}
