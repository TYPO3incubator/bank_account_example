<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Account\Command;

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

use H4ck3r31\BankAccountExample\Domain\Model\Iban\Iban;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Command\DomainCommand;

/**
 * AbstractAccountCommand
 */
abstract class AbstractAccountCommand implements DomainCommand
{
    /**
     * @var Iban
     */
    protected $iban;

    /**
     * @return Iban
     */
    public function getIban(): Iban
    {
        return $this->iban;
    }
}
