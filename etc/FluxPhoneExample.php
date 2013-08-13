<?php
require_once realpath(__DIR__.'/../vendor/autoload.php');

use SelvinOrtiz\Utils\Flux\Flux;
use SelvinOrtiz\Utils\Flux\Helper;

// The subject string (Phone)
$str	= '(612) 424-0013';

// Building the pattern (Fluently)
$flux	= Flux::getInstance()
		->startOfLine()
		->find('(')
		->digits(3)
		->then(')')
		->maybe(' ')
		->digits(3)
		->anyOf(' -')
		->digits(4)
		->endOfLine();

// Output the Flux instance
Helper::dump( $flux );

// Output the fluently built pattern (@see /src/SelvinOrtiz/Utils/Flux/Helper)
Helper::msg( $flux );

// Inspect the results
Helper::msg( $str );
Helper::msg( $flux->match( $str ) ? 'matched' : 'unmatched' );
Helper::msg( $flux->replace( '$2.$5.$7', $str ) );

//--------------------------------------------------------------------------------
// EOF
//--------------------------------------------------------------------------------
