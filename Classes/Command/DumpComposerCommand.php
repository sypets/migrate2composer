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
use Sypets\Migrate2composer\Composer\Typo3ComposerManifest;
use Sypets\Migrate2composer\Composer\Typo3Packages;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DumpComposerCommand extends Command
{

    /**
     * @var PackageManager
     */
    protected $packageManager;

    /**
     * @var Typo3Packages
     */
    protected $typo3Packages;

    /**
     * @var Typo3ComposerManifest
     */
    protected $typo3ComposerManifest;

    /**
     * @var array
     */
    protected $hints = [
        'Use "composer validate" to check the composer.json file',
        'Put the composer manifest (composer.json) in the project root directory, which should (usually) be one level above the web root directory',
        'Normalize your composer.json, see https://localheinz.com/blog/2018/01/15/normalizing-composer.json/',
        'Use documentation to help with migrating: https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/Index.html',
        'Work on a clone (copy) or schedule downtime while migrating!'
    ];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var bool
     */
    protected $batchMode = false;

    /**
     * @var SymfonyStyle
     */
    protected $io;


    public function __construct(string $name = null, PackageManager $packageManager = null,
        Typo3Packages $typo3Packages = null, Typo3ComposerManifest $typo3ComposerManifest = null)
    {
        parent::__construct($name);
        if (!$packageManager) {
            $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
        }
        if (!$typo3Packages) {
            $this->typo3Packages = GeneralUtility::makeInstance(Typo3Packages::class);
        }
        if (!$typo3ComposerManifest) {
            $this->typo3ComposerManifest = GeneralUtility::makeInstance(Typo3ComposerManifest::class);
        }
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Dump information about currently loaded extensions to screen.')
            ->addArgument('action', InputArgument::OPTIONAL,
                'Possible values: [all | manifest | commands]. If no argument is given, all is used.',
                'all')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED,
                'Load alternate composer.json template')
            ->addOption('batch-mode', 'b', InputOption::VALUE_NONE,
                'Make output usable for batch mode - do not output additional hints, etc.')
            ->addOption('version-constraint', 'c', InputOption::VALUE_REQUIRED,
                'Use the following value: exact | caret | tilde',
                Typo3Packages::VERSION_CONSTRAINT_EXACT);
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');
        $this->batchMode = $input->getOption('batch-mode');
        // show commands, e.g. composer require ...
        $showCommands = false;
        // show composer.json
        $showManifest = false;
        $versionConstraintType = $input->getOption('version-constraint');


        $this->showDescription();

        switch ($action) {
            case 'all':
            default:
                $showCommands = true;
                $showManifest = true;
                break;
            case 'manifest':
                $showManifest = true;
                break;
            case 'commands':
                $showCommands = true;
                break;
        }

        $packagesInfo = $this->typo3Packages->getInstalledPackages($versionConstraintType);
        $this->errors = $this->typo3Packages->getErrors();
        // remove this extension since it's no longer required and not available via Packagist
        if ($packagesInfo['sypets/migrate2composer'] ?? false) {
            unset($packagesInfo['sypets/migrate2composer']);
        }

        // load sample composer.json template
        $composerTemplatePath = $input->getOption('file') ?: '';
        $result = $this->typo3ComposerManifest->loadTemplateComposerJson($composerTemplatePath);
        if (!$this->batchMode && !$result) {
            $this->io->error('Could not load composer.json file: <' . $composerTemplatePath . '> ... abort.');
            return 1;
        }

        // Initialize composer.json as array
        $this->typo3ComposerManifest->initializeComposerManifest($packagesInfo);

        // dump composer manifest
        if ($showManifest) {
            if (!$this->batchMode) {
                $this->io->section('composer.json');
            }
            $this->io->writeln($this->typo3ComposerManifest->toString());
        }

        // dump composer commands: composer require ...
        if ($showCommands) {
            if (!$this->batchMode) {
                $this->io->section('Commands:');
            }
            foreach ($packagesInfo as $name => $values) {
                $this->io->writeln("composer require $name:" . $values['versionConstraint']);
            }
        }

        $this->showErrors();
        $this->showHints();

        return 0;
    }

    protected function showDescription()
    {
        if (!$this->batchMode) {
            $this->io->title($this->getDescription());
        }
    }

    protected function showErrors()
    {
        if ($this->errors && !$this->batchMode) {
            $this->io->section('Error & warnings:');
            // show errors
            foreach ($this->errors as $values) {
                if ($values['errorCode'] === AbstractMessage::ERROR) {
                    $this->io->error($values['errorMessage']);
                } else {
                    $this->io->warning($values['errorMessage']);
                }
            }
        }
    }

    protected function showHints()
    {
        if ($this->hints && !$this->batchMode) {
            $this->io->section('Hints:');
            $this->io->listing($this->hints);
        }
    }

}
