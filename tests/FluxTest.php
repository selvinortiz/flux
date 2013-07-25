<?php
use SelvinOrtiz\Utils\Flux\Flux;

class FluxTest extends PHPUnit_Framework_TestCase
{
	public function setUp()	{}

	public function tearDown() {}

	public function inspect($data)
	{
		fwrite( STDERR, print_r($data) );
	}

	public function testGetInstance()
	{
		$this->assertInstanceOf( 'SelvinOrtiz\\Utils\\Flux\\Flux', Flux::getInstance() );
	}

	public function testCompile()
	{
		$this->assertTrue( (string) Flux::getInstance() === '//' );
		$this->assertTrue( Flux::getInstance()->getPattern() === '//' );
	}

	public function testAddSeed()
	{
		$flux = Flux::getInstance()->addSeed( '/^(.*)$/' );
		$this->assertTrue( (string) $flux === '/^(.*)$/' );
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

	public function testAddPrefix()
	{
		$this->assertTrue( (string) Flux::getInstance()->startOfLine() === '/^/' );
	}

	public function testAddSuffix()
	{
		$this->assertTrue( (string) Flux::getInstance()->endOfLine() === '/$/' );
	}

	public function testModifiers()
	{
		$this->assertTrue( (string) Flux::getInstance()->multiline() === '//m');
		$this->assertTrue( (string) Flux::getInstance()->multiline()->ignoreCase() === '//mi');
		$this->assertTrue( (string) Flux::getInstance()->multiline()->ignoreCase()->matchNewLine() === '//mis');
	}

	public function testGetSegment()
	{
		$this->assertCount( 2, Flux::getInstance()->find('find')->then('again')->getSegments() );
		$this->assertCount( 2, Flux::getInstance()->find('find')->then('again')->removeSegment(5)->getSegments() );
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

	public function testOrTry()
	{
		$data	= 'dev.';
		$flux 	= Flux::getInstance()
				->startOfLine()
				->find('dev.')
				->orTry()
				->maybe('live.')
				->endOfLine();

		$this->assertTrue( (bool) $flux->match( $data ) );
		$this->assertTrue( $flux->replace( '$1', $data ) === 'dev.' );
	}

	public function testRange()
	{
		$flux = Flux::getInstance()->range('a', 'z', 0, 9);
		$this->assertTrue( $flux->getSegment() === '([a-z0-9])' );
	}

	//--------------------------------------------------------------------------------
	// @=Scenarios
	//--------------------------------------------------------------------------------

	public function testPhoneMatchReplace()
	{
		$phone	= '6124240013';
		$flux 	= Flux::getInstance()
				->startOfLine()
				->maybe('(')
				->digits(3)
				->maybe(')')
				->maybe(' ')
				->digits(3)
				->maybe('-')
				->digits(4)
				->endOfLine();

		$this->assertTrue( (bool) $flux->match( $phone ) );
		$this->assertTrue( $flux->replace( '($2) $5-$7', $phone ) === '(612) 424-0013' );
	}
}
