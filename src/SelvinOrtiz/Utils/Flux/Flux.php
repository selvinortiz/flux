<?php
namespace SelvinOrtiz\Utils\Flux;

/**
 * Fluent Regular Expressions in PHP
 *
 * @author         Selvin Ortiz - http://selv.in
 * @package        Flux
 * @version        0.5.2
 * @category       Regular Expressions (PHP)
 * @copyright      2013-2015 Selvin Ortiz
 */
class Flux
{
	/**
	 * The seed expression to use instead of a fluently defined one
	 *
	 * @note
	 * This is useful for testing
	 *
	 * @var string
	 */
	protected $seed;

	/**
	 * The regular expression components that make up the expression
	 * @var array
	 */
	protected $pattern   = array();
	protected $prefixes  = array();
	protected $suffixes  = array();
	protected $modifiers = array();

	/**
	 * @return Flux
	 */
	public static function getInstance()
	{
		return new self;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->compile();
	}

	/**
	 * @deprecated Deprecated for version 0.6.0
	 *
	 * @param string $seed
	 *
	 * @return $this
	 */
	public function addSeed($seed)
	{
		return $this->setSeed($seed);
	}

	/**
	 * Sets the seed expression to use instead of a fluently defined one
	 *
	 * @param $seed
	 *
	 * @return $this
	 */
	public function setSeed($seed)
	{
		$this->seed = $seed;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeSeed()
	{
		$this->seed = null;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSeed()
	{
		return $this->seed;
	}

	/**
	 * Compiles prefixes/pattern/suffixes/modifiers into a regular expression
	 *
	 * @return string
	 */
	protected function compile()
	{
		if (strlen(trim($this->seed)))
		{
			return $this->seed;
		}

		$pattern   = implode('', $this->pattern);
		$prefixes  = implode('', $this->prefixes);
		$suffixes  = implode('', $this->suffixes);
		$modifiers = implode('', $this->modifiers);

		return sprintf('/%s%s%s/%s', $prefixes, $pattern, $suffixes, $modifiers);
	}

	/**
	 * Alias of compile()
	 *
	 * @see compile()
	 *
	 * @return string
	 */
	public function getPattern()
	{
		return $this->compile();
	}

	/**
	 * Clears all pattern components to create a fresh expression
	 *
	 * @return $this
	 */
	public function clear()
	{
		$this->seed      = false;
		$this->pattern   = array();
		$this->prefixes  = array();
		$this->suffixes  = array();
		$this->modifiers = array();

		return $this;
	}

	/**
	 * Adds a sanitized pattern component that augments the overall expression
	 *
	 * @param string $value
	 * @param string $format
	 *
	 * @return $this
	 */
	public function add($value, $format = '(%s)')
	{
		array_push($this->pattern, sprintf($format, $this->sanitize($value)));

		return $this;
	}

	/**
	 * Adds a raw pattern component that augments the overall expression
	 *
	 * @param string $value
	 * @param string $format
	 *
	 * @return $this
	 */
	public function raw($value, $format = '%s')
	{
		array_push($this->pattern, sprintf($format, $value));

		return $this;
	}

	/**
	 * Adds a pattern component quantifier/length boundary
	 *
	 * @param int|null $min
	 * @param int|null $max
	 *
	 * @return Flux
	 */
	public function length($min = null, $max = null)
	{
		$lastSegmentKey = $this->getLastSegmentKey();

		if ($min && $max && $min > $max)
		{
			$lengthPattern = sprintf('{%d,%d}', (int) $min, (int) $max);
		}
		elseif ($min && !$max)
		{
			$lengthPattern = sprintf('{%d}', (int) $min);
		}
		else
		{
			$lengthPattern = '{1}';
		}

		return $this->replaceQuantifierByKey($lastSegmentKey, $lengthPattern);
	}

	/**
	 * @param int $position
	 *
	 * @return bool|string
	 */
	public function getSegment($position = 1)
	{
		$position = ($position > 0) ? --$position : 0;

		if (array_key_exists($position, $this->pattern))
		{
			return $this->pattern[$position];
		}

		return false;
	}

	/**
	 * @param int $position
	 *
	 * @return $this
	 */
	public function removeSegment($position = 1)
	{
		if (array_key_exists($position, $this->pattern))
		{
			unset($this->pattern[$position]);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getSegments()
	{
		return $this->pattern;
	}

	/**
	 * @return bool|mixed
	 */
	public function getLastSegmentKey()
	{
		if (count($this->pattern))
		{
			$patternKeys = array_keys($this->pattern);

			return array_shift($patternKeys);
		}

		return false;
	}

	/**
	 * Allows us to add quantifiers to the pattern created by the last method call
	 *
	 * @param string $key The key of the last segment in the pattern array
	 * @param string $replacement The quantifier to add to the previous pattern
	 *
	 * @return $this
	 */
	protected function replaceQuantifierByKey($key, $replacement = '')
	{
		$subject = $this->pattern[$key];

		if (strripos($subject, ')') !== false)
		{
			$subject             = rtrim($subject, ')');
			$subject             = $this->removeQuantifier($subject);
			$this->pattern[$key] = sprintf('%s%s)', $subject, $replacement);
		}
		else
		{
			$subject             = $this->removeQuantifier($subject);
			$this->pattern[$key] = sprintf('%s%s', $subject, $replacement);
		}

		return $this;
	}

	/**
	 * @param $pattern
	 *
	 * @return string
	 */
	protected function removeQuantifier($pattern)
	{
		if (strripos($pattern, '+') !== false && strripos($pattern, '\+') === false)
		{
			return rtrim($pattern, '+');
		}
		if (strripos($pattern, '*') !== false && strripos($pattern, '\*') === false)
		{
			return rtrim($pattern, '*');
		}
		if (strripos($pattern, '?') !== false && strripos($pattern, '\?') === false)
		{
			return rtrim($pattern, '?');
		}

		return $pattern;
	}

	/**
	 * @param $modifier
	 *
	 * @return $this
	 */
	public function addModifier($modifier)
	{
		if (!in_array($modifier, $this->modifiers))
		{
			array_push($this->modifiers, trim($modifier));
		}

		return $this;
	}

	/**
	 * @param $modifier
	 *
	 * @return $this
	 */
	public function removeModifier($modifier)
	{
		if (in_array($modifier, $this->modifiers))
		{
			unset($this->modifiers[$modifier]);
		}

		return $this;
	}

	/**
	 * @param $prefix
	 *
	 * @return $this
	 */
	public function addPrefix($prefix)
	{
		if (!in_array($prefix, $this->prefixes))
		{
			array_push($this->prefixes, trim($prefix));
		}

		return $this;
	}

	/**
	 * @param $suffix
	 *
	 * @return $this
	 */
	public function addSuffix($suffix)
	{
		if (!in_array($suffix, $this->suffixes))
		{
			array_push($this->suffixes, trim($suffix));
		}

		return $this;
	}

	//--------------------------------------------------------------------------------
	// @=MODIFIERS
	//--------------------------------------------------------------------------------

	public function startOfLine()
	{
		return $this->addPrefix('^');
	}

	public function endOfLine()
	{
		return $this->addSuffix('$');
	}

	public function ignoreCase()
	{
		return $this->addModifier('i');
	}

	/**
	 * @deprecated Deprecated for version 0.6.0
	 *
	 * @return Flux
	 */
	public function inAnyCase()
	{
		return $this->ignoreCase();
	}

	public function oneLine()
	{
		return $this->removeModifier('m');
	}

	/**
	 * @deprecated Deprecated for version 0.6.0
	 *
	 * @return Flux
	 */
	public function searchOneLine()
	{
		return $this->oneLine();
	}

	public function multiline()
	{
		return $this->addModifier('m');
	}

	public function matchNewLine()
	{
		return $this->addModifier('s');
	}

	public function dotAll()
	{
		return $this->matchNewLine();
	}

	//--------------------------------------------------------------------------------
	// @=LANGUAGE
	//--------------------------------------------------------------------------------

	public function find($value)
	{
		return $this->then($value);
	}

	public function then($value)
	{
		return $this->add($value);
	}

	public function maybe($value)
	{
		return $this->add($value, '(%s)?');
	}

	public function either()
	{
		return $this->raw(implode('|', func_get_args()), '(%s)');
	}

	public function any($value)
	{
		return $this->add($value, '([%s])');
	}

	public function anyOf($value)
	{
		return $this->any($value);
	}

	public function anything()
	{
		return $this->raw('(.*)');
	}

	public function anythingBut($value)
	{
		return $this->add($value, '([^%s]*)');
	}

	public function br()
	{
		return $this->raw('(\\n|\\r\\n)');
	}

	public function tab()
	{
		return $this->raw('(\\t)');
	}

	public function word()
	{
		return $this->raw('(\\w+)');
	}

	public function lineBreak()
	{
		return $this->br();
	}

	public function letters($min = null, $max = null)
	{
		if ($min && $max)
		{
			return $this->raw(sprintf('([a-zA-Z]{%d,%d})', $min, $max));
		}
		elseif ($min && is_null($max))
		{
			return $this->raw(sprintf('([a-zA-Z]{%d})', $min));
		}
		else
		{
			return $this->raw('([a-zA-Z]+)');
		}
	}

	public function digits($min = null, $max = null)
	{
		if ($min && $max)
		{
			return $this->raw(sprintf('(\\d{%d,%d})', $min, $max));
		}
		elseif ($min && is_null($max))
		{
			return $this->raw(sprintf('(\\d{%d})', $min));
		}
		else
		{
			return $this->raw('(\\d+)');
		}
	}

	public function orTry($value = '')
	{
		if (empty($value))
		{
			return $this->addPrefix('(')->addSuffix(')')->raw(')|(');
		}

		return $this->addPrefix('(')->addSuffix(')')->raw($value, ')|((%s)');
	}

	public function range()
	{
		$row    = 0;
		$args   = func_get_args();
		$ranges = array();

		foreach ($args as $segment)
		{
			$row++;

			if ($row % 2)
			{
				array_push($ranges, sprintf('%s-%s', $args[$row - 1], $args[$row]));
			}
		}

		return $this->raw(implode('', $ranges), '([%s])');
	}

	//--------------------------------------------------------------------------------
	// @=WORKERS
	//--------------------------------------------------------------------------------

	/**
	 * @param string $subject
	 * @param string $seed
	 *
	 * @return int
	 */
	public function match($subject, $seed = '')
	{
		if (!empty($seed))
		{
			$this->addSeed($seed);
		}

		return preg_match($this->compile(), $subject);
	}

	/**
	 * Performs a replacement by using numbered matches
	 *
	 * @param string $replacement
	 * @param string $subject
	 * @param string $seed
	 *
	 * @return mixed
	 */
	public function replace($replacement, $subject, $seed = '')
	{
		if (!empty($seed))
		{
			$this->addSeed($seed);
		}

		return preg_replace($this->compile(), $replacement, $subject);
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitize($value)
	{
		return preg_quote($value, '/');
	}
}
