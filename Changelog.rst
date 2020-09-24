=========
Changelog
=========

9.5.3
=====

also: *8.7.3*

* use exact version constraint by default instead of caret version constraint
* add option --version-constraint (-c) to specify how version constraints
  should be handled, e.g. with caret ('^1.0.0'), with tilde ('~1.0.0') or
  exact ('1.0.0)
* do not add migrate2composer to list of required extensions

9.5.2
=====

also: *8.7.2*

* skip extensions with errors: do not add them to list of required extensions,
  for example extensions with missing composer.json. In any case, a warning
  is displayed (if -b is not specified)
