<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlpiUsersTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 80],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 191],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 191],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'admin_jurnal'],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users', true);

        $this->db->table('users')->insert([
            'username'   => 'superadmin',
            'name'       => 'Super Admin',
            'email'      => 'superadmin@plpi.local',
            'password'   => password_hash('admin12345', PASSWORD_BCRYPT),
            'role'       => 'superadmin',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
