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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Typo3ComposerManifest
{
    /**
     * @var array
     */
    protected $composerManifest = [];

    /**
     * @param string $path
     * @return int
     */
    public function loadTemplateComposerJson(string $composerTemplatePath = '') : bool
    {
        // load template composer.json
        if (!$composerTemplatePath) {
            $composerTemplatePath = GeneralUtility::getFileAbsFileName('EXT:migrate2composer/Resources/Private/Composer/composer.json');
        }
        if ($composerTemplatePath) {
            if (file_exists($composerTemplatePath) && $composerContent = file_get_contents($composerTemplatePath)) {
                $this->composerManifest = \json_decode(
                    $composerContent,
                    true
                );
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $packagesInfo
     */
    public function initializeComposerManifest(array $packagesInfo)
    {
        if ($this->composerManifest) {
            $this->loadTemplateComposerJson();
        }

        // composer.json: set some defaults and automatic values
        //  This specifies the PHP version of the target system (uses only major.minor)
        $this->composerManifest['config']['platform']['php'] = preg_replace('#([0-9]+\.[0-9]+)\.[0-9]+#', '\1',
            \phpversion());

        // webdir (web root)
        $webDir = end(explode('/', Environment::getPublicPath()));
        if (!$webDir) {
            $webDir = 'public';
        }
        $this->composerManifest['extra']['typo3/cms']['web-dir'] = $webDir;

        // add require
        foreach ($packagesInfo as $name => $values) {
            $this->composerManifest['require'][$name] = $values['versionConstraint'];
        }
    }

    public function toString()
    {
        return self::convertComposerArrayToString($this->composerManifest);
    }


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
