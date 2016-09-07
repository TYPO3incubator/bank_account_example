<?php
namespace H4ck3r31\BankAccountExample\Domain\Property\TypeConverter;

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
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * IbanTypeConverter
 */
class IbanTypeConverter extends AbstractTypeConverter
{
    /**
   	 * @var int
   	 */
   	protected $priority = 10;

   	/**
   	 * @var string[]
   	 */
   	protected $sourceTypes = ['string'];

   	/**
   	 * @var string
   	 */
   	protected $targetType = Iban::class;

    /**
     * @param string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface|null $configuration
     * @return null|Iban
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL)
    {
        try {
            $iban = Iban::fromString($source);
        } catch (\Exception $exception) {
            $iban = null;
        }

        return $iban;
    }
}
