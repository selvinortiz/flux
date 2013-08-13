<?php
namespace SelvinOrtiz\Utils\Flux;

/**
 * @=SelvinOrtiz\Utils\Flux
 *
 * Fluent Regular Expressions in PHP
 *
 * @author		Selvin Ortiz - http://twitter.com/selvinortiz
 * @package		Tools
 * @version		0.5.2
 * @category	Regular Expressions (PHP)
 * @copyright	2013 Selvin Ortiz
 *
 * @todo
 * - Add source code comments
 * - Add language methods for more advanced usage
 * - Add support for quantifiers
 * - Add composer support
 */

class Flux
{
	protected $seed			= false;
	protected $pattern		= array();
	protected $prefixes		= array();
	protected $suffixes 	= array();
	protected $modifiers	= array();

	//--------------------------------------------------------------------------------

	public static function getInstance() { return new self;	}

	public function __toString() { return $this->compile();	}

	//--------------------------------------------------------------------------------

	/**
	 * @compile()
	 * Compiles the prefixes/pattern/suffixes/modifiers into a regular expression
	 *
	 * @return	[string]	The regular expression built by Flux or added with addSeed()
	 */
	protected function compile()
	{
		if ( $this->seed ) { return $this->seed; }

		$pattern	= implode( '', $this->pattern );
		$prefixes	= implode( '', $this->prefixes );
		$suffixes	= implode( '', $this->suffixes );
		$modifiers	= implode( '', $this->modifiers );

		return sprintf( '/%s%s%s/%s', $prefixes, $pattern, $suffixes, $modifiers );
	}

	public function getPattern() { return $this->compile(); }

	public function clear()
	{
		$this->seed			= false;
		$this->pattern		= array();
		$this->prefixes		= array();
		$this->suffixes 	= array();
		$this->modifiers	= array();
		return $this;
	}

	//--------------------------------------------------------------------------------

	/**
	 * @add()
	 * The core method to augment the pattern with a new segment
	 *
	 * @param	[string]	$val	The string to augment the pattern with
	 * @param	[string]	$frmt	The format string to wrap $val around
	 * @return	[object]	$this	Flux instance
	 */
	public function add( $val, $frmt='(%s)' )
	{
		array_push( $this->pattern, sprintf( $frmt, $this->sanitize($val) ) );
		return $this;
	}

	/**
	 * @raw()
	 * The core method to augment the pattern with a new segment w/o escaping
	 *
	 * @param	[string]	$val	The string to augment the pattern with
	 * @param	[string]	$frmt	The format string to wrap $val around
	 * @return	[object]	$this	Flux instance
	 */
	public function raw( $val, $frmt='%s' )
	{
		array_push( $this->pattern, sprintf( $frmt, $val ) );
		return $this;
	}

	public function length( $min=null, $max=null )
	{
		$lengthPattern	= '';
		$lastSegmentKey = $this->getLastSegmentKey();

		if ( $min && $max && $min > $max ) {
			$lengthPattern = sprintf( '{%d,%d}', (int) $min, (int) $max );
		} elseif ( $min && ! $max ) {
			$lengthPattern = sprintf( '{%d}', (int) $min );
		} else {
			$lengthPattern = '{1}';
		}

		return $this->replaceQuantifierByKey( $lastSegmentKey, $lengthPattern );
	}

	//--------------------------------------------------------------------------------

	public function addSeed( $seed )
	{
		$this->seed = $seed;
		return $this;
	}

	public function removeSeed()
	{
		$this->seed = false;
		return $this;
	}

	public function getSeed() { return $this->seed; }

	//--------------------------------------------------------------------------------

	public function getSegment( $position=1 )
	{
		$position = ($position > 0) ? --$position : 0;

		if ( array_key_exists( $position, $this->pattern ) ) {
			return $this->pattern[ $position ];
		}
		return false;
	}

	public function removeSegment( $position=1 )
	{
		if ( array_key_exists( $position, $this->pattern ) ) {
			unset($this->pattern[ $position ]);
		}
		return $this;
	}

	public function getSegments() { return $this->pattern; }

	public function getLastSegmentKey()
	{
		if ( count($this->pattern) ) {
			$patternKeys = array_keys( $this->pattern );
			return array_shift( $patternKeys );
		}

		return false;
	}

	//--------------------------------------------------------------------------------

	/**
	 * @replaceQuantifierByKey()
	 * Allows us to add quantifiers to the pattern created by the last method call
	 *
	 * @param	[int]		$key	The key of the last segment in the pattern array
	 * @param	[string]	$repl	The quantifier to add to the previous pattern
	 * @return	[object]	$this	The Flux instance
	 */
	protected function replaceQuantifierByKey( $key, $repl='' )
	{
		$subject = $this->pattern[ $key ];

		if ( strripos( $subject, ')' ) !== false ) {
			$subject = rtrim( $subject, ')' );
			$subject = $this->removeQuantifier( $subject );
			$this->pattern[ $key ] = sprintf( '%s%s)', $subject, $repl );
		} else {
			$subject = $this->removeQuantifier( $subject );
			$this->pattern[ $key ] = sprintf( '%s%s', $subject, $repl );
		}

		return $this;
	}

	protected function removeQuantifier( $pattern )
	{
		if ( strripos( $pattern, '+' ) !== false && strripos( $pattern, '\+' ) === false ) {
			return rtrim( $pattern, '+');
		}
		if ( strripos( $pattern, '*' ) !== false && strripos( $pattern, '\*' ) === false ) {
			return rtrim( $pattern, '*');
		}
		if ( strripos( $pattern, '?' ) !== false && strripos( $pattern, '\?' ) === false ) {
			return rtrim( $pattern, '?');
		}

		return $pattern;
	}

	//--------------------------------------------------------------------------------

	public function addModifier( $modifier )
	{
		if ( ! in_array( $modifier, $this->modifiers ) ) {
			array_push( $this->modifiers, trim($modifier) );
		}
		return $this;
	}

	public function removeModifier( $modifier )
	{
		if ( in_array($modifier, $this->modifiers) ) {
			unset( $this->modifiers[ $modifier] );
		}
		return $this;
	}

	//--------------------------------------------------------------------------------

	public function addPrefix( $prefix )
	{
		if ( ! in_array( $prefix, $this->prefixes ) ) {
			array_push( $this->prefixes, trim($prefix) );
		}
		return $this;
	}

	public function addSuffix( $suffix )
	{
		if ( ! in_array( $suffix, $this->suffixes ) ) {
			array_push( $this->suffixes, trim($suffix) );
		}
		return $this;
	}

	//--------------------------------------------------------------------------------
	// @=MODIFIERS
	//--------------------------------------------------------------------------------

	public function startOfLine() { return $this->addPrefix( '^' ); }

	public function endOfLine() { return $this->addSuffix( '$' ); }

	public function ignoreCase() { return $this->addModifier('i'); }

	// @TODO: Deprecate (0.6.0)
	public function inAnyCase() { return $this->ignoreCase(); }

	public function oneLine() { return $this->removeModifier('m'); }

	// @TODO: Deprecate (0.6.0)
	public function searchOneLine()	{ return $this->oneLine(); }

	public function multiline() { return $this->addModifier('m'); }

	public function matchNewLine() { return $this->addModifier('s'); }

	// @TODO: dotAll() vs matchNewLine() thoughts?
	public function dotAll() { return $this->matchNewLine(); }

	//--------------------------------------------------------------------------------
	// @=LANGUAGE
	//--------------------------------------------------------------------------------

	public function find( $val ) { return $this->then( $val ); }

	public function then( $val ) { return $this->add( $val ); }

	public function maybe( $val ) { return $this->add( $val, '(%s)?' ); }

	public function either()
	{
		return $this->raw( implode('|', func_get_args() ), '(%s)' );
	}

	public function any( $val ) { return $this->add( $val, '([%s])' ); }

	public function anyOf( $val ) { return $this->any( $val ); }

	public function anything() { return $this->raw( '(.*)' ); }

	public function anythingBut( $val ) { return $this->add( $val, '([^%s]*)' ); }

	public function br() { return $this->raw('(\\n|\\r\\n)'); }

	public function tab() { return $this->raw( '(\\t)' ); }

	public function word() { return $this->raw( '(\\w+)' ); }

	public function lineBreak() { return $this->br(); }

	public function letters( $min=null, $max=null )
	{
		if ($min && $max) {
			return $this->raw( sprintf( '([a-zA-Z]{%d,%d})', $min, $max ) );
		} elseif ( $min && is_null($max) ) {
			return $this->raw( sprintf( '([a-zA-Z]{%d})', $min ) );
		} else {
			return $this->raw( '([a-zA-Z]+)' );
		}
	}

	public function digits( $min=null, $max=null )
	{
		if ($min && $max) {
			return $this->raw( sprintf( '(\\d{%d,%d})', $min, $max ) );
		} elseif ( $min && is_null($max) ) {
			return $this->raw( sprintf( '(\\d{%d})', $min ) );
		} else {
			return $this->raw( '(\\d+)' );
		}
	}

	public function orTry( $val='' )
	{
		if ( empty($val) ) {
			return $this->addPrefix('(')->addSuffix(')')->raw( ')|(' );
		}

		return $this->addPrefix('(')->addSuffix(')')->raw( $val, ')|((%s)' );
	}

	// @TODO: Add some sanity check to the ranges
	public function range()
	{
		$row	= 0;
		$args	= func_get_args();
		$ranges	= array();

		foreach ($args as $segment) {
			$row++;
			if ($row % 2) {
				array_push( $ranges, sprintf( '%s-%s', $args[ $row-1 ], $args[ $row ] ) );
			}
		}

		return $this->raw( implode( '', $ranges ), '([%s])' );
	}

	//--------------------------------------------------------------------------------
	// @=WORKERS
	//--------------------------------------------------------------------------------

	/**
	 * @match()
	 * Tests the pattern to see if it matches $subject
	 *
	 * @return [boolean] Whether the string provided matches the pattern created/provided
	 */

	public function match( $subject, $seed='' )
	{
		if ( !empty($seed) ) { $this->addSeed( $seed ); }

		return preg_match( $this->compile(), $subject );
	}

	/**
	 * @replace()
	 * Performs a replacement by using numbered matches starting
	 *
	 * @return [string] The replaced string or a copy of the original
	 */

	public function replace( $replacement, $subject, $seed='' )
	{
		if ( !empty($seed) ) { $this->addSeed( $seed ); }

		return preg_replace( $this->compile(), $replacement, $subject );
	}

	/**
	 * @sanitize( $val )
	 * Allows us to add values to the pattern in a safe way
	 *
	 * @return [mix] The sanitized value
	 */
	public function sanitize( $val )
	{
		return preg_quote( $val, '/' );
	}
}
