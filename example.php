<?php
require __DIR__.'/Flux.php';

$url	= 'http://www.selvinortiz.com';
$flux	= new Flux();
$flux
	->startOfLine()
	->find('http')
	->maybe('s')
	->then('://')
	->maybe('www.')
	->anythingBut('.')
	->either('.co', '.com')
	->ignoreCase()
	->endOfLine();

dd( $flux, false );
msg( $flux );
msg( $flux->match( $url ) ? 'matched' : 'unmatched' );
msg( $flux->replace( 'https://$5$6', $url ) );
msg( '<hr>' );

//--------------------------------------------------------------------------------

$date	= 'Monday, Jul 22, 2013';
$flux	= new Flux();
$flux
	->startOfLine()
	->word()
	->then(', ')
	->letters(3)
	->then(' ')
	->digits(1, 2)
	->then(', ')
	->digits(4)
	->endOfLine();

dd( $flux, false );
msg( $flux );
msg( $flux->match( $date ) ? 'matched' : 'unmatched' );
msg( $flux->replace( '$3/$5/$7', $date ) );
msg( '<hr>' );

//--------------------------------------------------------------------------------

$phone	= '(612) 424-0013';
$flux	= new Flux();
$flux
	->startOfLine()
	->find('(')
	->digits(3)
	->then(')')
	->maybe(' ')
	->digits(3)
	->anyOf(' -')
	->digits(4)
	->endOfLine();

dd( $flux, false );
msg( $flux );
msg( $flux->match( $phone ) ? 'matched' : 'unmatched' );
msg( $flux->replace( '$2.$5.$7', $phone ) );
msg( '<hr>' );

//--------------------------------------------------------------------------------

function dd( $data, $die=true )
{
	echo '<pre style="font-weight: bold;">';
	print_r( $data );
	if ( $die ) { exit; }
	echo '</pre>';
}

function msg( $str )
{
	echo '<pre>';
	echo '<b>', $str, '</b>';
	echo '</pre>';
}
