<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration ini menambahkan kolom untuk menyimpan NAMA FILE (bukan file itu sendiri)
 * ke 3 tabel yang sudah ada. File fisiknya disimpan di folder public/uploads/,
 * sedangkan di database kita hanya simpan nama filenya saja.
 */
class AddFileColumns extends Migration
{
    public function up()
    {
        // Kolom foto untuk kamar
        $this->forge->addColumn('kamar', [
            'foto_kamar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'fasilitas',
            ],
        ]);

        // Kolom bukti transfer/bayar untuk pembayaran
        $this->forge->addColumn('pembayaran', [
            'bukti_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'tanggal_bayar',
            ],
        ]);

        // Kolom foto identitas (KTP/SIM) untuk pengajuan sewa dari pengunjung
        $this->forge->addColumn('pengajuan', [
            'foto_identitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'pesan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kamar', 'foto_kamar');
        $this->forge->dropColumn('pembayaran', 'bukti_bayar');
        $this->forge->dropColumn('pengajuan', 'foto_identitas');
    }
}
