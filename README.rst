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

* your installed system extensions (only active extensions will be considered)
* your installed third party extensions (only active extensions will be
  considered)
* the PHP version currently used
* the web root directory name
* if **helhum/typo3-console** is installed: in that case a "scripts" section
  will be added to your composer.json (if you use the default template)

What it does not detect
=======================

* whether the third party extensions are available on Packagist or TER. It
  just generally assumes, they are available on Packagist.

Important
=========

Do the actual migration process on a clone (copy) of your site or schedule a
downtime. Be sure to test it before you proceed in a live production
environment.

How to use this extension
=========================

1. You may want to remove unnecessary extensions first and clean up your site

2. Install the extension migrate2composer via TER (or from GitHub)::

       cd typo3conf/ext
       git clone https://github.com/sypets/migrate2composere.git

3. If you clone from Git, you may have to switch branches, e.g.::

       git checkout TYPO3_8-7

4. Activate **migrate2composer** in the Extension Manager

5. In the console (shell), go to your document root and run the following commands::

       migrate2composer:dump [-f <composer template file>] [action]

action can be:

* **all** (this is the default): shows all, including errors and hints
* **manifest**: this only dumps the composer.json file to the screen.
* **commands**: this only dumps the commands to the screen.

Show help::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -h

Dump all information to the screen::

   typo3/sysext/core/bin/typo3 migrate2composer:dump

If an extension does not have a composer.json file, this command will show an error,
for example::

   [ERROR] Composer manifest (composer.json) file of extension <widdelgrumpf> is missing

See also **Example output** below.

If the output looks good, you can directly write the composer.json file::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b manifest > ../composer.json

Make sure, the composer.json is valid before you proceed::

   cd ..
   composer validate

The extension uses `Resources/Private/Composer/composer.json` as a template. You can
create an alternative template and let the extension use this instead, for example::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b -f /var/tmp/composer.json manifest > ../composer.json


Alternatively, you can just dump the commands::

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b commands



How to proceed
==============

* Use the official documentation
  `Migrate TYPO3 Project to Composer <https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/Index.html>`__

You may want to test first, if it is possible to resolve the dependencies.

You can do this using the created composer.json file, e.g.::

   composer install --dry-run

You may see errors like this::

   Problem 11
    - The requested package somevendor/somepackage could not be found in any version, there may be a typo in the package name.

This means, the package is not available. Change your composer.json until all
dependencies can be resolved.

Additional steps
================

Migrate2composer currently does not detect if your third party extensions are available on Packagist.
For those that are not, you will need to make additional changes in your composer.json.
Again, see the official documentation
`Install Extension from Version Control System (e.g. GitHub, Gitlab, …) <https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/MigrationSteps.html#install-extension-from-version-control-system-e-g-github-gitlab>`__
and the following sections.

Example output
==============

run::

   php -f typo3/sysext/core/bin/typo3 migrate2composer:dump

output::

   Dump information about currently loaded extensions to screen.
   =============================================================

   Commands:
   ---------


   composer require friendsoftypo3/tt-address
   composer require georgringer/news
   composer require goran/save_close_ce
   composer require gridelementsteam/gridelements
   composer require in2code/powermail
   composer require netresearch/rte-ckeditor-image
   composer require sypets/migrate2composer
   composer require sypets/mytemplate
   composer require sypets/widdelgrumpf
   composer require typo3/cms-backend
   composer require typo3/cms-belog
   composer require typo3/cms-beuser
   composer require typo3/cms-core
   composer require typo3/cms-extbase
   composer require typo3/cms-extensionmanager
   composer require typo3/cms-filelist
   composer require typo3/cms-filemetadata
   composer require typo3/cms-fluid
   composer require typo3/cms-fluid-styled-content
   composer require typo3/cms-form
   composer require typo3/cms-frontend
   composer require typo3/cms-info
   composer require typo3/cms-install
   composer require typo3/cms-lowlevel
   composer require typo3/cms-opendocs
   composer require typo3/cms-recordlist
   composer require typo3/cms-recycler
   composer require typo3/cms-redirects
   composer require typo3/cms-reports
   composer require typo3/cms-rte-ckeditor
   composer require typo3/cms-scheduler
   composer require typo3/cms-seo
   composer require typo3/cms-setup
   composer require typo3/cms-t3editor
   composer require typo3/cms-tstemplate
   composer require typo3/cms-viewpage

   composer.json
   -------------

   {
       "name": "Add name ...",
       "description": "Add description ...",
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
           "sypets/migrate2composer": "^0.0.1",
           "sypets/mytemplate": "^1.3.2",
           "sypets/widdelgrumpf": "^0.0.1"
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
   * Work on a clone (copy) or schedule downtime while migrating!


Contact
=======

You can contact me on:

* https://typo3.slack.com (@sybille)
* https://twitter.com (@sypets)

Contribution via issues or pull requests is welcome in this repository.
