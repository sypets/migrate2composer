Why this extension?
===================

This is an extension which (partly) automates the job of creating a basic
composer.json for your site when you wish to migrate from a non-Composer site
to Composer.

How this works
==============

This extension will not actually install Composer or migrate your site to
Composer. The process is too risky without knowing everything there is to know
about your setup.

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

Important
=========

Do the actual migration process on a clone (copy) of your site or schedule a
downtime. Be sure to test it before you proceed in a live production
environment.

Don't forget to move typo3conf/sites to config/sites and typo3conf/l10n
to var/labels after the migration!

Migrate2composer automatically adds the extension typo3_console
(helhum/typo3-console) as it is currently essential and for example handles
the updating of typo3conf/PackageStates.php

How to use this extension
=========================

1. You may want to remove unnecessary extensions first and clean up your site

2. Install the extension migrate2composer via TER (or clone from GitHub to get latest dev version)
   Make sure you select the correct version which supports your TYPO3 version.

3. Activate **migrate2composer** in the Extension Manager

4. In the console (shell), go to your document root and run the following commands::

   typo3/sysext/core/bin/typo3 migrate2composer:dump

If an extension does not have a composer.json file, this command will show an error,
for example::

   [ERROR] Composer manifest (composer.json) file of extension <widdelgrumpf> is missing

See also **Example output** below.

In case of error, the extension will not be added to composer.json. You may have
to add the classloader information manually, e.g. ::

   "autoload": {
        "psr-4": {
            "Sypets\\Widdelgrumpf\\": "public/typo3conf/ext/widdelgrumpf/Classes/"
        }
    },

If the output looks good, you can directly write the composer.json file::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b manifest > ../composer.json


How to proceed
==============

* Use the official documentation
  `Migrate TYPO3 Project to Composer <https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/Index.html>`__

You may want to test first, if it is possible to resolve the dependencies.

You can do this using the created composer.json file, e.g.::

   cd ..
   composer validate
   composer install --dry-run

You may see errors like this::

   Problem 11
    - The requested package somevendor/somepackage could not be found in any version, there may be a typo in the package name.

This means, the package is not available. Change your composer.json until all
dependencies can be resolved.

Again, see the official documentation
`Install Extension from Version Control System (e.g. GitHub, Gitlab, …) <https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/MigrationSteps.html#install-extension-from-version-control-system-e-g-github-gitlab>`__
.

Once you are ready to migrate, these are the sample steps:

.. warning::

   This will result in outages - perform the steps on a copy! Make sure
   you have a backup.

1. Remove typo3, index.php and typo3conf/ext::

      rm -rf public/typo3 && rm -f public/index.php && rm -rf public/typo3conf/ext


2. Composer install::

      composer install

3. Remove migrate2composer and regenerate PackageStates.php::

      rm -rf public/typo3conf/ext/migrate2composer
      composer install

The files public/index.php and files in public/typo3 and public/typo3conf/ext
should now have been created.

4. Move some directories::

      mv public/typo3conf/sites config/sites
      mv public/typo3conf/l10n var/labels

The site should be available and fully functioning.

For more and additional steps, see the official documentation in the
"Installation Guide".


Commands
========

Run the commands with typo3/sysext/core/bin/typo3, e.g.::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -h

General::

   migrate2composer:dump [-f <composer template file>] [action]

Action can be:

* **all** (this is the default): shows all, including errors and hints
* **manifest**: this only dumps the composer.json file to the screen.
* **commands**: this only dumps the commands to the screen.

Show help::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -h

Dump all information to the screen::

   typo3/sysext/core/bin/typo3 migrate2composer:dump


Write the composer.json file::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b manifest > ../composer.json

The extension uses `Resources/Private/Composer/composer.json` as a template. You can
create an alternative template and let the extension use this instead, for example::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b -f /var/tmp/composer.json manifest > ../composer.json


Alternatively, you can just dump the commands::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b commands


Example output
==============

run::

   php -f typo3/sysext/core/bin/typo3 migrate2composer:dump

output::

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


Contact
=======

You can contact me on:

* https://typo3.slack.com (@sybille)
* https://twitter.com (@sypets)

Contribution via issues or pull requests is welcome in this repository.
