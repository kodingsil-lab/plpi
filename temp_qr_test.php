<?php
require __DIR__ . '/vendor/autoload.php';
$q = new Endroid\QrCode\QrCode('test');
var_dump(method_exists($q, 'setSize'));
var_dump(method_exists($q, 'setMargin'));
var_dump(method_exists($q, 'setText'));
var_dump(get_class($q));
