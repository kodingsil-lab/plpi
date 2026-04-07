<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressToPublishers extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('publishers')) {
            return;
        }

        if (! $this->db->fieldExists('address', 'publishers')) {
            $this->forge->addColumn('publishers', [
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'phone',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('publishers') && $this->db->fieldExists('address', 'publishers')) {
            $this->forge->dropColumn('publishers', 'address');
        }
    }
}
