<?php

declare(strict_types=1);

namespace Sypets\Migrate2composer\Composer;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ComposerUtility
{
    /**
     * This function removes empty arrays from an array.
     * Empty arrays may cause problems when writing composer.json
     * because they may be objects (associative arrays) or arrays
     * (numbered arrays). If they are empty, we cannot determine
     * the type and then the incorrect type may get used.
     *
     * @param array $a
     * @return void
     */
    public static function removeEmptyArrays(array &$a) : void
    {
        if (!$a || !is_array($a)) {
            return;
        }

        foreach ($a as $key => $value) {
            if (is_array($value) && !empty($value)) {
                self::removeEmptyArrays($value);
            }
            if (is_array($value) && empty($value)) {
                unset($a[$key]);
                continue;
            }
        }
    }

    /**
     * Return an array into a string that can be written as composer.json
     *
     * @param array $input
     * @return string
     */
    public static function convertComposerArrayToString(array $input) : string
    {
        // normalize array
        self::removeEmptyArrays($input);
        return \json_encode($input,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
