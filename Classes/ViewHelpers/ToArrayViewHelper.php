<?php
namespace H4ck3r31\BankAccountExample\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class ToArrayViewHelper extends AbstractViewHelper
{
    /**
     * @param object $subject
     * @return array
     */
    public function render($subject = null)
    {
        if ($subject === null) {
            $subject = $this->renderChildren();
        }

        if (!method_exists($subject, 'toArray')) {
            return [];
        }

        return $subject->toArray();
    }
}
