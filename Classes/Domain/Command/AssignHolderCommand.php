<?php
namespace H4ck3r31\BankAccountExample\Domain\Model;

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

use H4ck3r31\BankAccountExample\Controller\Common;

/**
 * AssignHolderCommand
 */
class AssignHolderCommand extends AbstractCommand
{
    /**
     * @return AssignHolderCommand
     */
    public static function instance()
    {
        return Common::getObjectManager()->get(AssignHolderCommand::class);
    }

    /**
     * @var string
     */
    protected $holder;

    /**
     * @param string $aggregateId
     * @param string $holder
     * @return AssignHolderCommand
     */
    public static function create(string $aggregateId, string $holder)
    {
        $command = static::instance();
        $command->aggregateId = $aggregateId;
        $command->holder = $holder;
        return $command;
    }

    public function getHolder()
    {
        return $this->holder;
    }
}
