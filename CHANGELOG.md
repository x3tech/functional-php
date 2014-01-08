# Changelog

##0.4.0 - 2014-01-08
- Add placeholder funtionality to curry
- Fix `Iterable::map` to allow passing in multiple arrays like native `array_map`
- Add `Iterable::zip` method, similar to Python's `zip` function 
  but pads with `null`
- Add `Iterable::multiPad` similar to `array_pad` but accepts an iterable of
  arrays, padding the other to the length of the longest one
- Add `Iterable::dictToPairs` function which does the opposite
  of `Iterable::pairsToDict`

##0.3.1 - 2014-01-06
- map\* methods now have the same signature as native method ($callback, $array)

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
