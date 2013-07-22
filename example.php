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
	->either('.in', '.co', '.com')
	->inAnyCase()
	->endOfLine();

dd( $flux, false );
// Pattern /^(http)(s)?(\:\/\/)(www\.)?([^\.]*)(.in|.co|.com)$/i
echo $flux->match( $url ) ? 'matched' : 'unmatched'; // matched
echo $flux->replace( 'https://$5$6', $url ); // https://selvinortiz.com

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
// Pattern /^(\()(\d{3})(\))( )?(\d{3})([ \-])(\d{4})$/
echo $flux->match( $phone ) ? 'matched' : 'unmatched'; // matched
echo $flux->replace( '$2.$5.$7', $phone ); // 612.424.0013


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
// Pattern /^(\()(\d{3})(\))( )?(\d{3})([ \-])(\d{4})$/
echo $flux->match( $date ) ? 'matched' : 'unmatched'; // matched
echo $flux->replace( '$3/$5/$7', $date ); // 612.424.0013

//--------------------------------------------------------------------------------

function dd($data, $die=true)
{
	echo '<pre style="font-weight: bold;">';
	print_r( $data );
	if ( $die ) { exit; }
	echo '</pre>';
}
