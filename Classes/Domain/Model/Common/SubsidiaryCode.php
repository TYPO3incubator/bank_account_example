<?php
namespace H4ck3r31\BankAccountExample\Domain\Model\Common;

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

use TYPO3\CMS\DataHandling\Core\Framework\Object\RepresentableAsString;

/**
 * SubsidiaryCode
 */
class SubsidiaryCode implements RepresentableAsString
{
    /**
     * @param string $subsidiaryCode
     * @param int $length
     * @param bool $numericOnly
     * @return SubsidiaryCode
     */
    public static function create(string $subsidiaryCode, int $length, bool $numericOnly)
    {
        if (strlen($subsidiaryCode) > $length) {
            throw new \InvalidArgumentException('Subsidiary code length exceeded');
        }
        if ($numericOnly && !is_numeric($subsidiaryCode)) {
            throw new \InvalidArgumentException('Subsidiary code must be numeric');
        }

        $subsidiaryCode = str_pad($subsidiaryCode, $length, '0', STR_PAD_LEFT);

        return new static($subsidiaryCode);
    }

    /**
     * @var string
     */
    private $subsidiaryCode;

    /**
     * @param string $subsidiaryCode
     */
    private function __construct(string $subsidiaryCode)
    {
        $this->subsidiaryCode = $subsidiaryCode;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->subsidiaryCode;
    }
}
