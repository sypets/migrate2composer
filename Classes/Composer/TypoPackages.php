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

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Typo3Packages
{
    const VERSION_CONSTRAINT_EXACT = 'exact';
    const VERSION_CONSTRAINT_CARET = 'caret';
    const VERSION_CONSTRAINT_TILDE = 'tilde';

    /**
     * @var PackageManager
     */
    protected $packageManager;

    /**
     * @var string
     */
    protected $versionPrefix = '';

    /**
     * @var array
     */
    protected $errors;

    public function __construct(PackageManager $packageManager = null)
    {
        if (!$packageManager) {
            $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
        }
    }

    public function setVersionConstraintType(string $versionConstraintType)
    {
        $this->versionPrefix = '';
        switch ($versionConstraintType) {
            case self::VERSION_CONSTRAINT_CARET:
                $this->versionPrefix = '^';
                break;
            case self::VERSION_CONSTRAINT_TILDE:
                $this->versionPrefix = '~';
                break;
        }

    }

    public function getInstalledPackages(string $versionConstraintType = self::VERSION_CONSTRAINT_EXACT) : array
    {
        $packagesInfo = [];
        $this->errors = [];
        $this->setVersionConstraintType($versionConstraintType);

        // collect information about active extensions
        $packages = $this->packageManager->getAvailablePackages();
        foreach ($packages as $package) {
            $key = $package->getPackageKey();

            if (!$this->packageManager->isPackageActive($key)) {
                // ignore inactive packages
                continue;
            }
            if ($package->getValueFromComposerManifest('type') === 'typo3-cms-framework') {
                $type = 'system';
            } else {
                $type = 'local';
            }
            $name = $package->getValueFromComposerManifest('name');

            if (!file_exists($package->getPackagePath() . 'composer.json')) {
                $this->errors[] = [
                    'errorCode' => AbstractMessage::WARNING,
                    'errorMessage' => 'Composer manifest (composer.json) file of extension <' . $key . '> is missing.'
                ];
                continue;
            } else {
                if (!preg_match('#^[^/]+/[^/]+$#', $name)) {
                    // check name
                    // -  https://getcomposer.org/doc/04-schema.md#name
                    // - MUST consist of <vendor>/<projectname>, any character is allowed
                    // - SHOULD only contain alphanumeric characters, no space
                    $this->errors[] = [
                        'errorCode' => AbstractMessage::WARNING,
                        'errorMessage' => 'Composer manifest (composer.json) file of extension <'
                            . $key . '> contains invalid name: <'
                            . $name . '>. Name should consist of <vendor/project>, e.g. helhum/typo3-console.'
                    ];
                    continue;
                }
            }
            $name = mb_strtolower($name);

            $packagesInfo[$name] = [
                'version' => $package->getValueFromComposerManifest('version'),
                'type' => $type
            ];
            $packagesInfo[$name]['versionConstraint'] = $this->versionPrefix . $packagesInfo[$name]['version'];

        }
        ksort($packagesInfo);

        return $packagesInfo;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

}
