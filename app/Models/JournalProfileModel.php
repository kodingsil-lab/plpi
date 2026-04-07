<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalProfileModel extends Model
{
    protected $table = 'journal_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['title','subtitle','cover_path','eissn','pissn','is_active','sort_order'];
}
