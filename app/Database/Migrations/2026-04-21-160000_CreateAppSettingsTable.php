<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'header_logo_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'login_logo_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'public_logo_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'favicon_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'app_timezone' => ['type' => 'VARCHAR', 'constraint' => 64, 'default' => 'Asia/Jakarta'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('app_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('app_settings', true);
    }
}
