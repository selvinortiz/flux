<?php
namespace SelvinOrtiz\Utils\Flux;

/**
 * @=SelvinOrtiz\Utils\Flux
 *
 * Fluent Regular Expressions in PHP
 *
 * @author		Selvin Ortiz - http://twitter.com/selvinortiz
 * @package		Tools
 * @version		0.4.5
 * @category	Regular Expressions (PHP)
 * @copyright	2013 Selvin Ortiz
 *
 * @todo
 * - Add source code comments
 * - Add language methods for more advanced usage
 * - Add support for array/array replacements
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

	public static function getInstance()
	{
		return new self;
	}

	public function __toString() { return $this->compile();	}

	protected function compile()
	{
		if ( $this->seed ) { return $this->seed; }

		$pattern	= implode( '', $this->pattern );
		$prefixes	= implode( '', $this->prefixes );
		$suffixes	= implode( '', $this->suffixes );
		$modifiers	= implode( '', $this->modifiers );

		return sprintf( '/%s%s%s/%s', $prefixes, $pattern, $suffixes, $modifiers );
	}

	public function addSeed( $seed )
	{
		$this->seed = $seed;
		return $this;
	}

	public function getSeed()
	{
		// Breaks the chain
		return $this->seed;
	}

	public function removeSeed()
	{
		$this->seed = false;
		return $this;
	}

	// Gets the segment in the pattern
	public function getSegment( $position=0 )
	{
		if ( array_key_exists( $position, $this->pattern ) ) {
			return $this->pattern[ $position ];
		}

		return false;
	}
	//--------------------------------------------------------------------------------
	// @=HELPERS
	//--------------------------------------------------------------------------------

	public function add( $val )
	{
		array_push( $this->pattern, $val );
		return $this;
	}

	public function raw( $val ) { return $this->add( sprintf( '(%s)', $val ) ); }

	public function addModifier( $modifier )
	{
		// @TODO: Define a method to make this operation safer and more expressive
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

	public function addPrefix( $prefix )
	{
		if ( ! in_array( $prefix, $this->prefixes ) ) {
			array_push( $this->prefixes, trim($prefix) );
		}

		return $this;
	}

	// @TODO: Run more tests on this method to find placement bugs if any
	public function addSuffix( $suffix )
	{
		if ( ! in_array( $suffix, $this->suffixes ) ) {
			$this->suffixes = array_merge( array( trim($suffix) ), $this->suffixes );
		}

		return $this;
	}

	//--------------------------------------------------------------------------------
	// @=MODIFIERS
	//--------------------------------------------------------------------------------

	public function startOfLine() { return $this->addPrefix( '^' ); }

	public function endOfLine() { return $this->addSuffix( '$' ); }

	public function ignoreCase() { return $this->addModifier('i'); }
	public function inAnyCase() { return $this->ignoreCase(); }

	public function searchOneLine()	{ return $this->removeModifier('m'); }

	public function multiline() { return $this->addModifier('m'); }

	public function dotAll() { return $this->addModifier('s'); }

	//--------------------------------------------------------------------------------
	// @=LANGUAGE
	//--------------------------------------------------------------------------------

	public function find( $val ) { return $this->then( $val ); }

	public function then( $val )
	{
		return $this->add( sprintf( '(%s)', $this->sanitize( $val) ) );
	}

	public function maybe( $val )
	{
		return $this->add( sprintf( '(%s)?', $this->sanitize( $val ) ) );
	}

	public function either( $val )
	{
		return $this->raw( implode('|', @func_get_args() ) );
	}

	public function any( $val )
	{
		return $this->add( sprintf( '([%s])', $this->sanitize($val) ) );
	}

	public function anyOf( $val ) { return $this->any( $val ); }

	public function anything() { return $this->add( '(.*)' ); }

	public function anythingBut( $val )
	{
		return $this->add( sprintf( '([^%s]*)', $this->sanitize( $val ) ) );
	}

	public function word() { return $this->add( '(\\w+)' ); }

	public function letters( $min=null, $max=null )
	{
		if ($min && $max) {
			return $this->add( sprintf( '([a-zA-Z]{%d,%d})', $min, $max ) );
		} elseif ( $min && is_null($max) ) {
			return $this->add( sprintf( '([a-zA-Z]{%d})', $min ) );
		} else {
			return $this->add( '([a-zA-Z]+)' );
		}
	}

	public function digits( $min=null, $max=null )
	{
		if ($min && $max) {
			return $this->add( sprintf( '(\\d{%d,%d})', $min, $max ) );
		} elseif ( $min && is_null($max) ) {
			return $this->add( sprintf( '(\\d{%d})', $min ) );
		} else {
			return $this->add( '(\\d+)' );
		}
	}

	public function orTry( $val )
	{
		return $this->addPrefix('(')->addSuffix(')')->add( sprintf( ')|(%s', $val ) );
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

		return $this->add( sprintf( '([%s])', implode( '', $ranges ) ) );
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
		// return preg_replace_callback( '/[^\w]/', function ($m) { return "\\".$m[0]; }, $val );
	}
}
