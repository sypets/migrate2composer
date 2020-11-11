=========
Changelog
=========

9.5.6
=====

Remove php from scripts section

The scripts section should use the command as used in the
typo3/cms-base-distribution package composer.json.


9.5.5
=====

Use the defaults from typo3/cms-base-distribution:

* **helhum/typo3-console** is added in any case. The scripts section is not
  removed from composer.json as this is necessary (for generating PackageStates.php
  etc.).
* Use caret version constraint by default

Add repository composer.typo3.org to enable fetching of extensions which
exist in TER, but not on Packagist. Generally, extensions **should** be
fetched from Packagist, but this can be used as a fallback.

9.5.4
=====

* Use tilde version constraint by default

9.5.3
=====

* use exact version constraint by default instead of caret version constraint
* add option --version-constraint (-c) to specify how version constraints
  should be handled, e.g. with caret ('^1.0.0'), with tilde ('~1.0.0') or
  exact ('1.0.0)
* do not add migrate2composer to list of required extensions

9.5.2
=====

* skip extensions with errors: do not add them to list of required extensions,
  for example extensions with missing composer.json. In any case, a warning
  is displayed (if -b is not specified)
