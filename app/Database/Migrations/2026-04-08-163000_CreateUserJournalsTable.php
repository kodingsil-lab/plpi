<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserJournalsTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('users') || ! $this->db->tableExists('journals')) {
            return;
        }

        if (! $this->db->tableExists('user_journals')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'journal_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'journal_id']);
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('user_journals', true);
        }

        if (! $this->db->fieldExists('journal_id', 'users')) {
            return;
        }

        $legacyAssignments = $this->db->table('users')
            ->select('id, journal_id')
            ->where('journal_id IS NOT NULL', null, false)
            ->get()
            ->getResultArray();

        if (empty($legacyAssignments)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $payload = [];
        foreach ($legacyAssignments as $item) {
            $userId = (int) ($item['id'] ?? 0);
            $journalId = (int) ($item['journal_id'] ?? 0);
            if ($userId <= 0 || $journalId <= 0) {
                continue;
            }
            $exists = $this->db->table('user_journals')
                ->where('user_id', $userId)
                ->where('journal_id', $journalId)
                ->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $payload[] = [
                'user_id' => $userId,
                'journal_id' => $journalId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($payload)) {
            $this->db->table('user_journals')->insertBatch($payload);
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_journals', true);
    }
}
