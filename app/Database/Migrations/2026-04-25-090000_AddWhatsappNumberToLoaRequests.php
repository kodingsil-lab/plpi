<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWhatsappNumberToLoaRequests extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('whatsapp_number', 'loa_requests')) {
            $this->forge->addColumn('loa_requests', [
                'whatsapp_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => true,
                    'after' => 'corresponding_email',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('whatsapp_number', 'loa_requests')) {
            $this->forge->dropColumn('loa_requests', 'whatsapp_number');
        }
    }
}
