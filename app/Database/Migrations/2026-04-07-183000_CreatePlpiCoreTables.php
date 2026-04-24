<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlpiCoreTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'logo_path'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('publishers', true);

        $this->forge->addField([
            'id'                     => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'publisher_id'           => ['type' => 'BIGINT', 'unsigned' => true],
            'name'                   => ['type' => 'VARCHAR', 'constraint' => 255],
            'code'                   => ['type' => 'VARCHAR', 'constraint' => 80],
            'slug'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'issn'                   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'e_issn'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'p_issn'                 => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'logo_path'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website_url'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'default_stamp_path'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'default_signer_name'    => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'default_signer_title'   => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'default_signature_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'pdf_sig_left_px'        => ['type' => 'INT', 'null' => true],
            'pdf_sig_top_px'         => ['type' => 'INT', 'null' => true],
            'pdf_sig_height_px'      => ['type' => 'INT', 'null' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('publisher_id', 'publishers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('journals', true);

        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'journal_id'          => ['type' => 'BIGINT', 'unsigned' => true],
            'request_code'        => ['type' => 'VARCHAR', 'constraint' => 80],
            'article_url'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'article_id_external' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'title'               => ['type' => 'TEXT'],
            'authors_json'        => ['type' => 'LONGTEXT'],
            'corresponding_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'whatsapp_number'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'affiliations_json'   => ['type' => 'LONGTEXT', 'null' => true],
            'volume'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'issue_number'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'published_year'      => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
            'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'notes_admin'         => ['type' => 'TEXT', 'null' => true],
            'rejection_reason'    => ['type' => 'TEXT', 'null' => true],
            'approved_at'         => ['type' => 'DATETIME', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('request_code');
        $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('loa_requests', true);

        $this->forge->addField([
            'id'                  => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'journal_id'          => ['type' => 'BIGINT', 'unsigned' => true],
            'loa_request_id'      => ['type' => 'BIGINT', 'unsigned' => true],
            'loa_number'          => ['type' => 'VARCHAR', 'constraint' => 120],
            'article_url'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'article_id_external' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'title'               => ['type' => 'TEXT'],
            'authors_json'        => ['type' => 'LONGTEXT'],
            'corresponding_email' => ['type' => 'VARCHAR', 'constraint' => 191],
            'affiliations_json'   => ['type' => 'LONGTEXT', 'null' => true],
            'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published'],
            'verification_hash'   => ['type' => 'VARCHAR', 'constraint' => 191],
            'public_token'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'pdf_path'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'published_at'        => ['type' => 'DATETIME', 'null' => true],
            'revoked_at'          => ['type' => 'DATETIME', 'null' => true],
            'revoked_reason'      => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('loa_number');
        $this->forge->addUniqueKey('verification_hash');
        $this->forge->addUniqueKey('public_token');
        $this->forge->addForeignKey('journal_id', 'journals', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('loa_request_id', 'loa_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('loa_letters', true);

        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'loa_letter_id'   => ['type' => 'BIGINT', 'unsigned' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => 'menunggu'],
            'sent_to_email'   => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'sent_at'         => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('loa_letter_id', 'loa_letters', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('loa_notifications', true);

        $this->forge->addField([
            'id'         => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 191],
            'subtitle'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cover_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'eissn'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pissn'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('journal_profiles', true);
    }

    public function down()
    {
        $this->forge->dropTable('journal_profiles', true);
        $this->forge->dropTable('loa_notifications', true);
        $this->forge->dropTable('loa_letters', true);
        $this->forge->dropTable('loa_requests', true);
        $this->forge->dropTable('journals', true);
        $this->forge->dropTable('publishers', true);
    }
}
