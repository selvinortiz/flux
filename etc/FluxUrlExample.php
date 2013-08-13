<?php
require_once realpath(__DIR__.'/../vendor/autoload.php');

use SelvinOrtiz\Utils\Flux\Flux;
use SelvinOrtiz\Utils\Flux\Helper;

// The subject string (URL)
$str	= 'http://www.selvinortiz.com';

// Building the pattern (Fluently)
$flux	= Flux::getInstance()
		->startOfLine()
		->find('http')
		->maybe('s')
		->then('://')
		->maybe('www.')
		->anythingBut('.')
		->either('.co', '.com')
		->ignoreCase()
		->endOfLine();

// Output the Flux instance
Helper::dump( $flux );

// Output the fluently built pattern (@see /src/SelvinOrtiz/Utils/Flux/Helper)
Helper::msg( $flux );

// Inspect the results
Helper::msg( $str );
Helper::msg( $flux->match( $str ) ? 'matched' : 'unmatched' );
Helper::msg( $flux->replace( 'https://$5$6', $str ) );

//--------------------------------------------------------------------------------
// EOF
//--------------------------------------------------------------------------------
