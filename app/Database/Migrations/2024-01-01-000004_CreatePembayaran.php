<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaran extends Migration
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
            'penyewa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bulan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'Nama bulan pembayaran, misal: Januari',
            ],
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'jumlah_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['lunas', 'belum_lunas'],
                'default'    => 'lunas',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('penyewa_id', 'penyewa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembayaran');
    }

    public function down()
    {
        $this->forge->dropForeignKey('pembayaran', 'pembayaran_penyewa_id_foreign');
        $this->forge->dropTable('pembayaran');
    }
}
