<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJournalIdToUsersTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        if ($this->db->fieldExists('journal_id', 'users')) {
            return;
        }

        $this->forge->addColumn('users', [
            'journal_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
                'after' => 'role',
            ],
        ]);
    }

    public function down()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        if (! $this->db->fieldExists('journal_id', 'users')) {
            return;
        }

        $this->forge->dropColumn('users', 'journal_id');
    }
}
