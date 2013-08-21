<?php
require __DIR__.'/../vendor/autoload.php';

$zit = SelvinOrtiz\Zit\Zit::getInstance();

$zit->bind( 'myService', function( $zit ) { return new stdClass; } );

echo '<pre>';
echo '<hr>';
print_r( $zit );
print_r( $zit->myService() );
echo '<hr>';
echo '</pre>';
