<?php
require_once realpath(__DIR__.'/../vendor/autoload.php');

use SelvinOrtiz\Utils\Flux\Flux;

class FluxTest extends PHPUnit_Framework_TestCase
{
	public function setUp()	{}

	public function tearDown() {}

	public function testAddSeedToString()
	{
		$flux = Flux::getInstance()->addSeed('/^(.*)$/');
		$this->assertTrue( (string) $flux === '/^(.*)$/' );
	}

	public function testAddSeed()
	{
		$flux = Flux::getInstance()->addSeed('/^(.*)$/');
		$this->assertTrue( $flux->getSeed() === '/^(.*)$/' );
	}

	public function testGetSeed()
	{
		$flux = Flux::getInstance()->addSeed('/^(.*)$/');
		$this->assertTrue( $flux->getSeed() === '/^(.*)$/' );
	}

	public function testRemoveSeed()
	{
		$flux = Flux::getInstance()->addSeed('/^(.*)$/')->removeSeed();
		$this->assertFalse( $flux->getSeed() );
	}

	public function testSanitize()
	{
		$value = Flux::getInstance()->sanitize('/.word');
		$this->assertTrue( $value === '\/\.word' );
	}

	public function testFind()
	{
		$flux = Flux::getInstance()->find('required');
		$this->assertTrue( $flux->getSegment() === '(required)' );
	}

	public function testThen()
	{
		$flux = Flux::getInstance()->then('required');
		$this->assertTrue( $flux->getSegment() === '(required)' );
	}

	public function testMaybe()
	{
		$flux = Flux::getInstance()->maybe('optional');
		$this->assertTrue( $flux->getSegment() === '(optional)?' );
	}

	public function testAny()
	{
		$flux = Flux::getInstance()->any('abc');
		$this->assertTrue( $flux->getSegment() === '([abc])' );
	}

	public function testAnything()
	{
		$flux = Flux::getInstance()->anything();
		$this->assertTrue( $flux->getSegment() === '(.*)' );
	}

	public function testAnythingBut()
	{
		$flux = Flux::getInstance()->anythingBut('.');
		$this->assertTrue( $flux->getSegment() === '([^\.]*)' );
	}

	public function testEither()
	{
		$flux = Flux::getInstance()->either('one', 'two', 'three');
		$this->assertTrue( $flux->getSegment() === '(one|two|three)' );
	}

	public function testRange()
	{
		$flux = Flux::getInstance()->range('a', 'z', 0, 9);
		$this->assertTrue( $flux->getSegment() === '([a-z0-9])' );
	}
}
