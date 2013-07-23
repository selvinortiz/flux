## FLUX (Fluent Regex) 0.5.0 (Beta)
*by* [Selvin Ortiz](http://twitter.com/selvinortiz)

### Description
Fluent Regular Expressions _in_ PHP

### Requirements
- PHP 5.3
- Composer

### Install
`Flux` is available as a [package](https://packagist.org/packages/selvinortiz/flux) via [composer](http://getcomposer.org)

* require: `"selvinortiz/flux": "dev-master"`
* autoload: `require_once 'path/to/vendor/autoload.php'`
* namespace: `use SelvinOrtiz\Utils\Flux\Flux;`
* instantiate: `$flux = Flux::getInstance();`

_You can additionally clone/download this repo and do whatever you want: )_

----

### @Changelog

----
#### 0.5.0 (Beta)
- Adds `getSegments()` to improve testability [Issue #5](https://github.com/selvinortiz/flux/issues/5)
- Adds composer package [selvinortiz/flux](https://packagist.org/packages/selvinortiz/flux)
- Adds `dev` branch
- Adds contributing notes
- Adds install notes

----

#### 0.4.5
- Fixes internal namespace conflict
- Changes namespace from `Sortiz\Tools` to `SelvinOrtiz\Utils\Flux`
- Adds composer support [Issue #3](https://github.com/selvinortiz/flux/issues/3)
- Adds the `addSeed()` and `removeSeed()` methods [Issue #4](https://github.com/selvinortiz/flux/issues/4)
- Adds the `getInstance()` static method
- Adds `FluxUrlExample.php`, `FluxDateExample.php`, and `FluxPhoneExample.php`
- Adds `getSeed()` to get the seed without forcing `__toString` on the object
- Adds `getSegment()` to extract a segment (capturing group) from the pattern
- Implements unit tests (60% coverage) [Issue #3](https://github.com/selvinortiz/flux/issues/3)
- Implements Full `PSR-2` Compliance (Tabs over Spaces)
- Enables the `seed` on `match()` and `replace()` [Issue #4](https://github.com/selvinortiz/flux/issues/4)
- Removes `example.php` and defines them elsewhere
- Moves examples into `/etc` and defines one example per file
- Other small fixes and additions

----
#### 0.4.0
- Adds `Flux` to the `Sortiz\Tools` namespace
- Implements `PSR-2` Compliance (Tabs over Spaces)
- Updates version number on `Flux` and this readme file
- Updates the class instantiation with fully qualified class name on `example.php`
- Adds references to other repos that have ported `flux`
- Addresses concerns outlined in [Issue #3](https://github.com/selvinortiz/flux/issues/3)

----
#### 0.3.0
- Improves documentation with `phone/date` examples
- Adds the `letters()` method
- Renames the `numbers()` method to `digits()`
- Adds support for quantifiers for `digits()`
- Adds `ignoreCase()` and promotes it above `inAnyCase()`
- Improves the documented API

_Thought hard about changing the name to `FluentX` any thoughts?_

----
#### 0.2.0
- Adds the `either( $option1, $option2 [, $option3 ...] )` method to handle OR cases
- Updates the *fluent* example in this readme file
- Adds the license

----
#### 0.1.0 (Alpha)
Initial preview release

----

### @Todo
- Add source code comments
- Add support for quantifiers
- Add language methods for more advanced use cases
- Add support for array/array replacements
- Add reference to repos that have ported `Flux` (*)
- Add license notes (*)
- Add contributing notes
- Add credits

### @Example
This simple example illustrates the way you would use `flux` and it's fluent interface to build complex patterns.

```php
require_once realpath(__DIR__.'/../vendor/autoload.php');

use SelvinOrtiz\Utils\Flux\Flux;
use SelvinOrtiz\Utils\Flux\Helper;

// The subject string (URL)
$str	= 'http://www.selvinortiz.com';

// Bulding the pattern (Fluently)
$flux	= Flux::getInstance()
		->startOfLine()
		->find('http')
		->maybe('s')
		->then('://')
		->maybe('www.')
		->anythingBut('.')
		->either('.co', '.com')
		->ignoreCase()
		->endOfLine();

// Output the Flux instance
Helper::dump( $flux );

// Output the fluently built pattern (@see /src/SelvinOrtiz/Utils/Flux/Helper)
Helper::msg( $flux );

// Inspect the results
Helper::msg( $str );
Helper::msg( $flux->match( $str ) ? 'matched' : 'unmatched' );
Helper::msg( $flux->replace( 'https://$5$6', $str ) );
```
_For other examples, please see the `/etc` directory.

### FLUX API
The **flux** API was designed to give you a _fluent chainable object_ to build patterns with.

#### `startOfLine()`
Adds a beginning of line `^` modifier

#### `endOfLine()`
Adds an end of line `$` modifier

#### `find( $val ) & then( $val )`
Allow you to augment the pattern with a required `segment` and it escapes regular expression characters

#### `maybe( $val )`
Allows you to augment the pattern with an optional `segment`

#### `any( $val ) & anyOf( $val )`
Allow you to create a set of characters to match

#### `anything()`
Adds a *wild card* `(.*)` `segment` to the pattern but it does not make `dotAll()` explicit

#### `anythingBut( $val )`
Will match anything but the characters in `$val` which is opposite of `any()` and `anyOf`

#### `word()`
Adds `(\w+)` to the pattern which will match a single word

#### `letters( $min=null, $max=null )`
Only matches characters in the alphabet and uses `$min` and `$max` to create a quantifier

#### `digits( $mix=null, $max=null )`
Only matches digits and uses `$min` and `$max` to create a quantifier like `word()`

#### `range( $from, $to [, $from, $to ...])`
Allows you to create a `range` character class like `a-z0-9` by calling `range('a', 'z', 0, 9)`

#### `orTry()`
This is experimental and I don't have the implementation I feel comfortable with... yet!

#### `ignoreCase() & inAnyCase()`
Adds the `i` modifier to the pattern which will allow you to match in a case insensitive manner

#### `dotAll()`
Adds the `s` modifier to the pattern which will allow you to match a `new line` when using `anything()`

#### `multiline()`
Adds the `m` modifier to the pattern which will allow you to search across multiple lines

#### `searchOneLine()`
Removes the modifier added by `multiline()` if it was previously called

#### `match( $subject )`
Simply takes your `$subject` in, compares it against the pattern, and returns whether a it matched or not

#### `replace( $replacement, $subject )`
You can replace matched `segments` by using the `$x` format where `x` is the `(int)` position of the matched `segment`

----

### Flux Elsewhere
There is interest in porting `Flux` to other languages/platforms like `NodeJS`, `Groovy` and `Java` they'll be listed here once available.

* [NodeJS](https://npmjs.org/package/node-flux) _by_ [James Brooks](http://james.brooks.so)

### Feedback
This is something that started as a weekend experiment but I would love to take it further so if you have any suggestions, please fire away!

_The best way to get in touch with me is via twitter [@selvinortiz](http://twitter.com/selvinortiz) we'll take if from there_ :)

### Contributing
1. Check for open issues or open a new issue for a feature request or a bug
2. Fork this repo to start making your changes to the `dev` branch or branch off
3. Write a test which shows that the bug was fixed or that the feature works as expected
4. Send a pull request and bug me until I merge it or tell you _no cigar; )_


### MIT License
*Flux* is released under the [MIT license](http://opensource.org/licenses/MIT) which pretty much means you can do with it as you please and I won't get mad because I'm that nice; )
