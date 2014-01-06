# Changelog

##0.3.0 - 2014-01-06
- Features
  - Implement `Debug::tap` for debugging function chains
    (`var_dump`s and returns what's passed to it)
  - Implement `Debug::tapCb` returns a callback to `Debug::tap`
- Bugs
  - Fix missing namespace declaration in Map.php
  - Fix calls to `Iterable::map`
- Misc
  - Implement unit tests
  - Add travis-ci configuration
  - Use short array syntax
  - Fix code to adhere to PSR-1 and PSR-2

##0.2.0-dev - 2013-12-21
- Move various functionality into separate files
- Remove array specific methods and only use the ones for iterables
- Document all methods
