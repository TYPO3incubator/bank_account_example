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
 * BranchCode
 */
class BranchCode implements RepresentableAsString
{
    /**
     * @var string
     */
    private $branchCode;

    /**
     * @param string $branchCode
     * @param int $length
     * @param bool $numericOnly
     * @return BranchCode
     */
    public static function create(string $branchCode, int $length, bool $numericOnly)
    {
        if (strlen($branchCode) > $length) {
            throw new \InvalidArgumentException('Branch code length exceeded');
        }
        if ($numericOnly && !is_numeric($branchCode)) {
            throw new \InvalidArgumentException('Branch code must be numeric');
        }

        $branchCode = str_pad($branchCode, $length, '0', STR_PAD_LEFT);

        return new static($branchCode);
    }

    /**
     * @param string $branchCode
     */
    private function __construct(string $branchCode)
    {
        $this->branchCode = $branchCode;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->branchCode;
    }
}
