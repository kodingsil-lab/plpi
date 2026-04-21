<?php
require 'vendor/autoload.php';

echo "email.host=" . env('email.host') . "\n";
echo "email.port=" . env('email.port') . "\n";
echo "email.protocol=" . env('email.protocol') . "\n";
echo "CI_ENVIRONMENT=" . env('CI_ENVIRONMENT') . "\n";
?>
