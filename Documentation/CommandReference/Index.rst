.. include:: /Includes.rst.txt

=================
Command Reference
=================

.. contents:: Table of contents (on this page)
   :local:
   :depth: 1

Introduction
============

Run the commands with typo3/sysext/core/bin/typo3, e.g.::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -h

General::

   migrate2composer:dump [-f <composer template file>] [action]

Action can be:

* **all** (this is the default): shows all, including errors and hints
* **manifest**: this only dumps the composer.json file to the screen.
* **commands**: this only dumps the commands to the screen.

Commands
========

Show help
---------

.. code-block:: shell

   typo3/sysext/core/bin/typo3 migrate2composer:dump -h

Basic command
-------------

Dump all information to the screen

.. code-block:: shell

   typo3/sysext/core/bin/typo3 migrate2composer:dump


Write the composer.json file

.. code-block:: shell

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b manifest > ../composer.json

Use different base composer.json
--------------------------------

The extension uses `Resources/Private/Composer/composer.json` as a template. You can
create an alternative template and let the extension use this instead, for example:

.. code-block:: shell

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b -f /var/tmp/composer.json manifest > ../composer.json

Show commands
-------------

Alternatively, you can just dump the composer commands to create a composer.json
file (or rather modify an existing basic composer.json file).

.. code-block:: shell

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b commands


Example output
==============

run

.. code-block:: shell

   php -f typo3/sysext/core/bin/typo3 migrate2composer:dump

output:

.. code-block:: none

   Dump information about currently loaded extensions to screen.
   =============================================================

   Commands:
   ---------


   composer require friendsoftypo3/tt-address:^5.1.2
   composer require georgringer/news:^8.3.0
   composer require goran/save_close_ce:^1.0.4
   composer require gridelementsteam/gridelements:^9.5.0
   composer require helhum/typo3-console:^5.5.5
   composer require in2code/powermail:^7.4.0
   composer require netresearch/rte-ckeditor-image:^9.0.4
   composer require sypets/mytemplate:^1.3.2
   composer require typo3/cms-backend:^9.5.20
   composer require typo3/cms-belog:^9.5.20
   composer require typo3/cms-beuser:^9.5.20
   composer require typo3/cms-core:^9.5.20
   composer require typo3/cms-extbase:^9.5.20
   composer require typo3/cms-extensionmanager:^9.5.20
   composer require typo3/cms-filelist:^9.5.20
   composer require typo3/cms-filemetadata:^9.5.20
   composer require typo3/cms-fluid:^9.5.20
   composer require typo3/cms-fluid-styled-content:^9.5.20
   composer require typo3/cms-form:^9.5.20
   composer require typo3/cms-frontend:^9.5.20
   composer require typo3/cms-info:^9.5.20
   composer require typo3/cms-install:^9.5.20
   composer require typo3/cms-lowlevel:^9.5.20
   composer require typo3/cms-opendocs:^9.5.20
   composer require typo3/cms-recordlist:^9.5.20
   composer require typo3/cms-recycler:^9.5.20
   composer require typo3/cms-redirects:^9.5.20
   composer require typo3/cms-reports:^9.5.20
   composer require typo3/cms-rte-ckeditor:^9.5.20
   composer require typo3/cms-scheduler:^9.5.20
   composer require typo3/cms-seo:^9.5.20
   composer require typo3/cms-setup:^9.5.20
   composer require typo3/cms-t3editor:^9.5.20
   composer require typo3/cms-tstemplate:^9.5.20
   composer require typo3/cms-viewpage:^9.5.20

   composer.json
   -------------

   {
       "name": "vendor/mysite",
       "description": "Add description ...",
       "license": [
          "GPL-2.0-or-later"
       ],
       "authors": {
          "name": "Author name",
          "email": "nouser@example.com"
       },
       "repositories": [],
       "autoload": {
          "psr-4": [],
          "classmap": []
       },
       "config": {
           "platform": {
               "php": "7.3"
           }
       },
       "extra": {
           "typo3/cms": {
               "web-dir": "htdocs"
           }
       },
       "require": {
           "friendsoftypo3/tt-address": "^5.1.2",
           "georgringer/news": "^8.3.0",
           "goran/save_close_ce": "^1.0.4",
           "gridelementsteam/gridelements": "^9.5.0",
           "in2code/powermail": "^7.4.0",
           "netresearch/rte-ckeditor-image": "^9.0.4",
           "sypets/mytemplate": "^1.3.2",
           "typo3/cms-backend": "^9.5.20",
           "typo3/cms-belog": "^9.5.20",
           "typo3/cms-beuser": "^9.5.20",
           "typo3/cms-core": "^9.5.20",
           "typo3/cms-extbase": "^9.5.20",
           "typo3/cms-extensionmanager": "^9.5.20",
           "typo3/cms-filelist": "^9.5.20",
           "typo3/cms-filemetadata": "^9.5.20",
           "typo3/cms-fluid": "^9.5.20",
           "typo3/cms-fluid-styled-content": "^9.5.20",
           "typo3/cms-form": "^9.5.20",
           "typo3/cms-frontend": "^9.5.20",
           "typo3/cms-info": "^9.5.20",
           "typo3/cms-install": "^9.5.20",
           "typo3/cms-lowlevel": "^9.5.20",
           "typo3/cms-opendocs": "^9.5.20",
           "typo3/cms-recordlist": "^9.5.20",
           "typo3/cms-recycler": "^9.5.20",
           "typo3/cms-redirects": "^9.5.20",
           "typo3/cms-reports": "^9.5.20",
           "typo3/cms-rte-ckeditor": "^9.5.20",
           "typo3/cms-scheduler": "^9.5.20",
           "typo3/cms-seo": "^9.5.20",
           "typo3/cms-setup": "^9.5.20",
           "typo3/cms-t3editor": "^9.5.20",
           "typo3/cms-tstemplate": "^9.5.20",
           "typo3/cms-viewpage": "^9.5.20",
       },
       "scripts": {
            "typo3-cms-scripts": [
                "typo3cms install:fixfolderstructure",
                "typo3cms install:generatepackagestates"
            ],
            "post-autoload-dump": [
                "@typo3-cms-scripts"
            ]
       }
   }

   Error & warnings:
   -----------------

   [WARNING] Composer manifest (composer.json) file of extension <widdelgrumpf> is missing.
   [WARNING] Composer manifest (composer.json) file of extension <logger> contains invalid name: <My
             Logger>. Name should consist of <vendor/project>, e.g. helhum/typo3-console.

   Hints:
   ------

   * Your composer manifest (composer.json) should be in the project root directory, which should (usually) be one level above the web root directory (htdocs)
   * Normalize your composer.json, see https://localheinz.com/blog/2018/01/15/normalizing-composer.json/
   * Use documentation to help with migrating: https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/Index.html

