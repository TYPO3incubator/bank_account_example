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

use H4ck3r31\BankAccountExample\Domain\Model\Common\CommandException;
use H4ck3r31\BankAccountExample\Domain\Model\Common\ValueObjectException;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * AbstractController
 */
abstract class AbstractController extends ActionController
{
    /**
     * @var array
     */
    protected $finalRedirect;

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

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response) {
        try {
            parent::processRequest($request, $response);
        } catch (ValueObjectException $exception) {
            $this->addFlashMessage($exception->getMessage());
        } catch (CommandException $exception) {
            $this->addFlashMessage($exception->getMessage());
        }

        if (!empty($this->finalRedirect)) {
            call_user_func_array([$this, 'redirect'], $this->finalRedirect);
        }
    }
}
