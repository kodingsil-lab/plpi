<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class TestController extends BaseController
{
    public function config()
    {
        $config = config('Email');
        echo "SMTPHost: " . $config->SMTPHost . "\n";
        echo "SMTPPort: " . $config->SMTPPort . "\n";
        echo "SMTPUser: " . $config->SMTPUser . "\n";
        echo "protocol: " . $config->protocol . "\n";
        echo "SMTPTimeout: " . $config->SMTPTimeout . "\n";
    }
}
?>
