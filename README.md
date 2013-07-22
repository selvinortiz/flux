## FLUX (Fluent Regular Expressions)
*by* [Selvin Ortiz](http://twitter.com/selvinortiz)

### Description
Fluent Regular Expressions _for_ PHP

----

### Changelog

----
#### 0.1.0
Initial preview release

----

### Example
----

```php
$flux		= new Flux();
$subject 	= 'http://selvinortiz.com';

$flux
	->startOfLine()
	->find('http')
	->maybe('s')
	->then('://')
	->maybe('www.')
	->anythingBut('.')
	->raw('.com|.co')
	->inAnyCase()
	->endOfLine();

// Echoing the instance will yield the compiled pattern (__toString)
echo $flux; // /^(http)(s)?(\:\/\/)(www\.)?([^\.]*)(.com|.co)$/i

// Match against the subject string
echo $flux->match( $subject ); // TRUE

// Replace the subject with matched segment $5 and $6
echo $flux->replace( '$5$6', $subject ); // selvinortiz.com
```

### FLUX API
The **flux** API was designed to give you a *fluent chainable object to build patterns with*.

#### `startOfLine()`
Adds a beginning of line `^` modifier

#### `endOfLine()`
Adds an end of line `$` modifier

#### `find( $val )`
The first `segment` in the pattern, also an alias to `then( $val )`

#### `then( $val )`
Allows you to augment the pattern with a required segment and like `find()`, it escapes regular expression chars

#### `maybe( $val )`
Allows you to augment the pattern with an optional segment

#### `any()`
Adds a *wild card* `(.*)` segment to the pattern but it does not make `dotAll()` explicit

#### `anyOf()`
Alias to `any()`

#### `match( $subject )`

#### `replace( $replacement, $subject )`

#### `(...)`
There are plenty of other function to document but I'll get to the rest shortly
