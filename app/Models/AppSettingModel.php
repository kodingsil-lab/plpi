<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table = 'app_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'header_logo_path',
        'login_logo_path',
        'public_logo_path',
        'favicon_path',
        'app_timezone',
    ];
}
