<?php
namespace H4ck3r31\BankAccountExample\Controller;

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

use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * AbstractController
 */
abstract class AbstractController extends ActionController
{
    /**
     * Allows property mapping for data-transfer-object arguments.
     */
    protected function initializeAction()
    {
        parent::initializeAction();

        foreach ($this->arguments as $argument) {
            if (!StringUtility::endsWith($argument->getName(), 'Dto')) {
                continue;
            }
            $argument->getPropertyMappingConfiguration()
                ->allowAllProperties()
                ->setTypeConverterOption(
                    PersistentObjectConverter::class,
                    PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
                    true
                );
        }
    }
}
