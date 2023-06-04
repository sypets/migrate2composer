.. include:: /Includes.rst.txt

============
Introduction
============

This is a TYPO3 extension which helps to migrate a non-Composer TYPO3
installation to Composer. It can create a composer.json based on currently
installed extensions.

How this works
==============

This extension will not actually install Composer or migrate your site to
Composer.

The extension automatically detects some information about your site, makes
educated guesses about the rest and uses current best practices to generate a
composer.json file which you can use to migrate to Composer.

Alternatively, it just dumps the commands, such as
`composer require typo3/cms-core`, etc.

It does this by detecting which extensions are currently installed in your TYPO3
installation.

Because **migrate2composer** does not actually change anything itself, it is
safe to use and you can make the necessary changes yourself, doing this in a
controlled environment.

This is what the extension detects
==================================

* your installed system extensions and version (only active extensions will
  be considered)
* your installed third party extensions and version (only active extensions
  will be considered)
* the PHP version currently used
* the web root directory name

What it does not detect
=======================

* whether the third party extensions are available on Packagist or TER. It
  just generally assumes, they are available.
