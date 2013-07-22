<?php
require __DIR__.'/Flux.php';

$flux 		= new Flux();
$subject 	= 'http://selvinortiz.com';

$flux
	->startOfLine()
	->then('http')					// $1
	->maybe('s')					// $2
	->then('://')					// $3
	->maybe('www.')					// $4
	->anythingBut('.')				// $5
	->either('.in', '.co', '.com') 	// $6
	->inAnyCase()
	->endOfLine();

dd( $flux.'', false );

dd( $flux->match( $subject) ? 'Matched' : 'Unmatched', false );
dd( $flux->replace( '$5$6', $subject ) );

//--------------------------------------------------------------------------------

function dd($data, $die=true)
{
	echo '<pre style="font-weight: bold;">';
	print_r( $data );
	if ( $die ) { exit; }
	echo '</pre>';
}
