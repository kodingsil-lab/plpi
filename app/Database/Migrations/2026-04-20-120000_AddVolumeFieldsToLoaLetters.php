<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVolumeFieldsToLoaLetters extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('volume', 'loa_letters')) {
            $fields['volume'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }

        if (! $this->db->fieldExists('issue_number', 'loa_letters')) {
            $fields['issue_number'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }

        if (! $this->db->fieldExists('published_year', 'loa_letters')) {
            $fields['published_year'] = ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('loa_letters', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('volume', 'loa_letters')) {
            $this->forge->dropColumn('loa_letters', 'volume');
        }

        if ($this->db->fieldExists('issue_number', 'loa_letters')) {
            $this->forge->dropColumn('loa_letters', 'issue_number');
        }

        if ($this->db->fieldExists('published_year', 'loa_letters')) {
            $this->forge->dropColumn('loa_letters', 'published_year');
        }
    }
}
