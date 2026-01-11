<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * 建立 tracer 資料表用於追蹤 fmd* 資料的修改歷史
 */
class CreateTracerTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tracer01' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tracer02' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => '資料表名稱',
            ],
            'tracer03' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => '記錄ID',
            ],
            'tracer04' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => '操作類型 (INSERT/UPDATE/DELETE)',
            ],
            'tracer05' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '舊資料 (JSON)',
            ],
            'tracer06' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '新資料 (JSON)',
            ],
            'tracer07' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => '操作人員ID',
            ],
            'tracer08' => [
                'type'    => 'DATETIME',
                'comment' => '操作時間',
            ],
            'tracer09' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => '備註',
            ],
        ]);

        $this->forge->addKey('tracer01', true);
        $this->forge->addKey('tracer02');
        $this->forge->addKey('tracer03');
        $this->forge->addKey('tracer04');
        $this->forge->addKey('tracer07');
        $this->forge->addKey('tracer08');

        $this->forge->createTable('tracer');
    }

    public function down()
    {
        $this->forge->dropTable('tracer');
    }
}
