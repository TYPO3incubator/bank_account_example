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

use TYPO3\CMS\DataHandling\Core\Domain\Model\Common\RepresentableAsString;

/**
 * NationalCode
 */
class NationalCode implements RepresentableAsString
{
    /**
     * @param string $nationalCode
     * @return static
     */
    public static function create(string $nationalCode)
    {
        if (strlen($nationalCode) > 2) {
            throw new ValueObjectException('National code length exceeded');
        }

        return new static($nationalCode);
    }

    /**
     * @var string
     */
    private $nationalCode;

    /**
     * @param string $nationalCode
     */
    private function __construct(string $nationalCode)
    {
        $this->nationalCode = $nationalCode;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->nationalCode;
    }

    public function verify(NationalCode $nationalCode)
    {
        if ($this->nationalCode !== (string)$nationalCode) {
            throw new \RuntimeException('National code mismatching ' . $this->nationalCode);
        }
    }
}
