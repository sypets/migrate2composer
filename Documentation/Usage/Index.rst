.. include:: /Includes.rst.txt

=====
Usage
=====

.. attention::

   *  Do the actual migration process on a clone (copy) of your site or schedule a
      downtime. Be sure to test it before you proceed in a live production
      environment.
   *  Don't forget to move typo3conf/sites to config/sites and typo3conf/l10n
      to var/labels after the migration!
   *  Your current directory structure should be so, that your document root
      is in a subdirectory of your project directory, e.g.

      .. code-block:: none

         myproject/
         └── public
            ├── typo3_src => link to source
            └── typo3conf

      If this is not the case, change this before proceeding!

.. tip::

   Use the official TYPO3 documentation on how to migrate to Composer for the
   manual steps:
   `Migrate TYPO3 Project to Composer <https://docs.typo3.org/m/typo3/guide-installation/master/en-us/MigrateToComposer/Index.html>`__


.. _usage_quickstart:

Quickstart
==========

*short version*

.. code-block:: shell

   typo3/sysext/core/bin/typo3 migrate2composer:dump -b manifest > ../composer.json

Now perform additional steps to migrate to composer, see :ref:`usage_how_to_proceed`.

How to use this extension
=========================

*long version*

1. You may want to remove unnecessary extensions first and clean up your site

2. Install the extension migrate2composer

3. In the console (shell), go to your document root and run the following command:

   .. code-block:: shell

      typo3/sysext/core/bin/typo3 migrate2composer:dump

   If an extension does not have a composer.json file, this command will show an error,
   for example:

   .. code-block:: shell

      [ERROR] Composer manifest (composer.json) file of extension <widdelgrumpf> is missing

   See also **Example output** below.

   In case of error, the extension will not be added to composer.json. You may have
   to add the classloader information manually later, e.g.

   .. code-block:: json

      "autoload": {
           "psr-4": {
               "Sypets\\Widdelgrumpf\\": "public/typo3conf/ext/widdelgrumpf/Classes/"
           }
       }

5. If the output looks good, you can directly write the composer.json file

   .. code-block:: shell

      typo3/sysext/core/bin/typo3 migrate2composer:dump -b manifest > ../composer.json

6. Now perform additional steps to migrate to composer, see :ref:`usage_how_to_proceed`.

.. _usage_how_to_proceed:

How to proceed
==============

You may want to test first, if it is possible to resolve the dependencies.

You can do this using the created composer.json file, e.g.

.. code-block:: shell

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

1. Uninstall and remove migrate2composer

2. Remove typo3, index.php and typo3conf/ext::

      rm -rf public/typo3 && rm -f public/index.php && rm -rf public/typo3conf/ext


3. Composer install::

      composer install


4. Move some directories::

      mv public/typo3conf/sites config/sites
      mv public/typo3conf/l10n var/labels

The site should be available and fully functioning.

