<?php
namespace SelvinOrtiz\Utils\Flux;

// This file can be safely removed, it is not used by Flux.php
// It is only used in the examples @ /etc

class Helper
{
	public static function dump($data, $die=false)
	{
		echo '<pre>';
		print_r($data);
		if ( $die ) { exit; }
		echo '</pre>';
	}

	// This forces the __toString method if an object is passed
	public static function msg($data, $die=false)
	{
		echo '<pre>';
		echo $data;
		if ( $die ) { exit; }
		echo '</pre>';
	}
}
