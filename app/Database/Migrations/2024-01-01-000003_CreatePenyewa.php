<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenyewa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kamar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'alamat_asal' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kamar_id', 'kamar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penyewa');
    }

    public function down()
    {
        $this->forge->dropForeignKey('penyewa', 'penyewa_kamar_id_foreign');
        $this->forge->dropTable('penyewa');
    }
}
