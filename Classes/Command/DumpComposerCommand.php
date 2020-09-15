<?php

namespace Sypets\Migrate2composer\Command;

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Sypets\Migrate2composer\Composer\ComposerUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DumpComposerCommand extends Command
{
    /**
     * @var PackageManager
     */
    protected $packageManager;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Dump information about currently loaded extensions to screen.')
            ->addArgument('action', InputArgument::OPTIONAL,
                'Possible values: [all | manifest | commands]. If no argument is given, all is used.')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED,
                'Load alternate composer.json template')
            ->addOption('batch-mode', 'b', InputOption::VALUE_NONE,
                'Make output usable for batch mode - do not output additional hints, etc.');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composerInfo = [];
        $errors = [];
        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action') ?: 'all';
        $batchMode = $input->getOption('batch-mode');
        if (!$batchMode) {
            $io->title($this->getDescription());
            switch ($action) {
                case 'all':
                    $io->note("Shows all necessary commands and dumps sample composer.json file");
                    break;
                case 'manifest':
                    $io->note("Dumps sample composer.json file");
                    break;
                case 'commands':
                    $io->note("Shows all necessary commands");
                    break;
            }
        }
        $packages = $this->packageManager->getAvailablePackages();

        // load template composer.json
        $composerTemplatePath = $input->getOption('file') ?: GeneralUtility::getFileAbsFileName('EXT:migrate2composer/Resources/Private/Composer/composer.json');
        if ($composerTemplatePath) {
            if (file_exists($composerTemplatePath) && $composerContent = file_get_contents($composerTemplatePath)) {
                $composerManifest = \json_decode(
                    $composerContent,
                    true
                );
            } else {
                if (!$batchMode) {
                    $io->error('Could not load composer.json file: <' . $composerTemplatePath . '> ... abort.');
                }
                return 1;
            }
        }

        // composer.json: set some defaults and automatic values
        //  This specifies the PHP version of the target system (uses only major.minor)
        $composerManifest['config']['platform']['php'] = preg_replace('#([0-9]+\.[0-9]+)\.[0-9]+#', '\1',
            \phpversion());
        $webDir = end(explode('/', Environment::getPublicPath()));
        if (!$webDir) {
            $webDir = 'public';
        }
        $composerManifest['extra']['typo3/cms']['web-dir'] = $webDir;

        $hints = [
            'Your composer manifest (composer.json) should be in the project root directory, which should (usually) be one level above the web root directory ('
            . $webDir
            . ')',
            'Normalize your composer.json, see https://localheinz.com/blog/2018/01/15/normalizing-composer.json/',
            'Use documentation to help with migrating: https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/Index.html',
            'Work on a clone (copy) or schedule downtime while migrating!'
        ];

        // collect information about active extensions
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
                $errors[] = [
                    'errorCode' => AbstractMessage::WARNING,
                    'errorMessage' => 'Composer manifest (composer.json) file of extension <' . $key . '> is missing.'
                ];
            } else {
                if (!preg_match('#^[^/]+/[^/]+$#', $name)) {
                    // check name
                    // -  https://getcomposer.org/doc/04-schema.md#name
                    // - MUST consist of <vendor>/<projectname>, any character is allowed
                    // - SHOULD only contain alphanumeric characters, no space
                    $errors[] = [
                        'errorCode' => AbstractMessage::WARNING,
                        'errorMessage' => 'Composer manifest (composer.json) file of extension <'
                            . $key . '> contains invalid name: <'
                            . $name . '>. Name should consist of <vendor/project>, e.g. helhum/typo3-console.'
                    ];
                }
            }
            $name = mb_strtolower($name);

            $composerInfo[$name] = [
                'cmd' => "composer require $name",
                'version' => $package->getValueFromComposerManifest('version'),
                'type' => $type
            ];

        }
        ksort($composerInfo);

        // if typo3_console is not installed, remove typo3-cms-scripts section
        if (!isset($composerInfo['helhum/typo3-console'])) {
            unset($composerManifest['scripts']['typo3-cms-scripts']);
            if (isset($composerManifest['scripts']['post-autoload-dump'])) {
                foreach ($composerManifest['scripts']['post-autoload-dump'] as $key => $post) {
                    if ($post === '@typo3-cms-scripts') {
                        unset($composerManifest['scripts']['post-autoload-dump'][$key]);
                    }
                }
                if (!$composerManifest['scripts']['post-autoload-dump']) {
                    unset($composerManifest['scripts']['post-autoload-dump']);
                }
            }
            $hints[] = 'Install and use typo3_console';
        }

        // dump composer commands
        if (!$batchMode && ($action === 'all' || $action === 'commands')) {
            $io->section('Commands:');
        }
        foreach ($composerInfo as $name => $values) {
            if ($action === 'all' || $action === 'commands') {
                $io->writeln($values['cmd']);
            }
            if ($values['type'] === 'system') {
                $composerManifest['require'][$name] = "^" . TYPO3_version;
            } else {
                $composerManifest['require'][$name] = '^' . $values['version'];
            }
        }

        // dump composer manifest
        if ($action === 'all' || $action === 'manifest') {
            if (!$batchMode) {
                $io->section('composer.json');
            }
            $io->writeln(ComposerUtility::convertComposerArrayToString($composerManifest));
        }

        // show errors
        if ($errors && !$batchMode) {
            $io->section('Error & warnings:');
            // show errors
            foreach ($errors as $values) {
                if ($values['errorCode'] === AbstractMessage::ERROR) {
                    $io->error($values['errorMessage']);
                } else {
                    $io->warning($values['errorMessage']);
                }
            }
        }

        // show hints
        if ($hints && !$batchMode) {
            $io->section('Hints:');
            $io->listing($hints);

        }
        return 0;
    }
}
