## FLUX (Fluent Regex) 0.5.1 (Stable)
*by* [Selvin Ortiz](http://twitter.com/selvinortiz)
[![Build Status](https://travis-ci.org/selvinortiz/flux.png)](https://travis-ci.org/selvinortiz/flux)
[![Total Downloads](https://poser.pugx.org/selvinortiz/flux/d/total.png)](https://packagist.org/packages/selvinortiz/flux)
[![Latest Stable Version](https://poser.pugx.org/selvinortiz/flux/v/stable.png)](https://packagist.org/packages/selvinortiz/flux)


### Description
Fluent Regular Expressions _in_ PHP inspired by and largely based on
[VerbalExpressions:JS](https://github.com/jehna/VerbalExpressions) by
[Jesse Luoto](https://plus.google.com/u/0/101155583332851062944/posts)

*@see inspiration & credits below for more info.*

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

### @Example
This simple example illustrates the way you would use `flux` and it's fluent interface to build complex patterns.

```php
require_once realpath(__DIR__.'/../vendor/autoload.php');

use SelvinOrtiz\Utils\Flux\Flux;
use SelvinOrtiz\Utils\Flux\Helper;

// The subject string (URL)
$str	= 'http://www.selvinortiz.com';

// Building the pattern (Fluently)
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
Helper::msg( $flux ); // /^(http)(s)?(\:\/\/)(www\.)?([^\.]*)(.co|.com)$/i

// Inspect the results
Helper::msg( $str );
Helper::msg( $flux->match( $str ) ? 'matched' : 'unmatched' );
Helper::msg( $flux->replace( 'https://$5$6', $str ) );
```
_For other examples, please see the `/etc` directory._

----

### @Changelog

----
#### 0.5.1
- Adds `getSegments()` which was not included in `0.5.0` [Issue #5](https://github.com/selvinortiz/flux/issues/5)
- Adds `removeSegment()` which can be used in unit tests as well
- Adds `lineBreak()` and `br()` which matches a new line (DOS/Unix)
- Adds `clear()` which allows you to clear out the pattern and start from scratch
- Adds `getPattern()` which compiles the expression and returns it
- Adds `deprecation candidates as @todos
- Fixes mixed logic between `add()` and `raw()`
- Fixes implementation on the `orTry()` method
- Moves example in readme above `changelog`
- Improves unit tests

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
- Add reference to repos that have ported `Flux` (*)
- Add license notes (*)
- Add contributing notes (*)
- Add credits (*)

### FLUX API
The **flux** API was designed to give you a _fluent chainable object_ to build patterns with.

#### `startOfLine()`
Adds a beginning of line `^` modifier

#### `endOfLine()`
Adds an end of line `$` modifier

#### `find( $val ) & then( $val )`
Allows you to augment the pattern with a required `segment` and it escapes regular expression characters

#### `maybe( $val )`
Allows you to augment the pattern with an optional `segment`

#### `any( $val ) & anyOf( $val )`
Allows you to create a set of characters to match

#### `anything()`
Adds a *wild card* `(.*)` `segment` to the pattern but it does not make `dotAll()` explicit

#### `anythingBut( $val )`
Will match anything but the characters in `$val` which is opposite of `any()` and `anyOf`

#### `br() & lineBreak()`
Allows you to match a new line `(DOS/Unix)`

#### `tab()`
Adds a `(\t)` to the pattern which will match a tab

#### `word()`
Adds `(\w+)` to the pattern which will match a single word

#### `letters( $min=null, $max=null )`
Only matches characters in the alphabet and uses `$min` and `$max` to create a quantifier

#### `digits( $mix=null, $max=null )`
Only matches digits and uses `$min` and `$max` to create a quantifier like `word()`

#### `range( $from, $to [, $from, $to ...])`
Allows you to create a `range` character class like `a-z0-9` by calling `range('a', 'z', 0, 9)`

#### `orTry( $val='' )`
Allows you to create OR cases `(this)|(else)` and retain the capturing order to use in `replace()`

#### `ignoreCase() & inAnyCase()`
Adds the `i` modifier to the pattern which will allow you to match in a case insensitive manner

#### `matchNewLine() & dotAll()`
Adds the `s` modifier to the pattern which will allow you to match a `new line` when using `anything()`

#### `multiline()`
Adds the `m` modifier to the pattern which will allow you to search across multiple lines

#### `oneLine() & searchOneLine()`
Removes the modifier added by `multiline()` if it was previously called

#### `match( $subject )`
Simply takes your `$subject` in, compares it against the pattern, and returns whether a it matched or not

#### `replace( $replacement, $subject )`
You can replace matched `segments` by using the `$x` format where `x` is the `(int)` position of the matched `segment`

#### `getPattern()`
Returns the compiled pattern which you can also get by using the `flux` instance in a context where `__toString()` will be called

#### `clear()`
Clears the created `pattern` along with the `modifiers`, `prefixes`, and `suffixes`

----

### Flux Elsewhere
There is a straight port of *Flux* for [NodeJS](https://npmjs.org/package/node-flux) _by_ [James Brooks](http://james.brooks.so) whom has also collaborated on this project.

### Feedback
This is something that started as a weekend experiment but I would love to take it further so if you have any suggestions, please fire away!

_The best way to get in touch with me is via twitter [@selvinortiz](http://twitter.com/selvinortiz) we'll take if from there_ :)

### Contributing
1. Check for open issues or open a new issue for a feature request or a bug
2. Fork this repo to start making your changes to the `dev` branch or branch off
3. Write a test which shows that the bug was fixed or that the feature works as expected
4. Send a pull request and bug me until I merge it or tell you _no cigar; )_


### Inspiration & Credits
This project is inspired and largely based on
[VerbalExpressions:JS](https://github.com/jehna/VerbalExpressions) by
[Jesse Luoto](https://plus.google.com/u/0/101155583332851062944/posts)
whom on *July 20, 2013* started a weekend project that generated a lot of interest in the developer community and that project has proven to have a lot of potential.

*Flux* is not a straight port of *VerbalExpressions* but if you're interested in a straight *VerbalExpressions* port for *PHP* you should checkout
[VerbalExpressions:PHP](https://github.com/markwilson/VerbalExpressionsPhp) by
[Mark Wilson](https://github.com/markwilson)

*VerbalExpressions* has also been ported to `Ruby`, `Java`, `Groovy` as of this update (July 25, 2013).

*For a little background as to why _flux_ was created and why you should use it, please refer to* [Issue #7](https://github.com/selvinortiz/flux/issues/7)
*for a discussion on that matter.*

### MIT License
*Flux* is released under the [MIT license](http://opensource.org/licenses/MIT) which pretty much means you can do with it as you please and I won't get mad because I'm that nice; )
