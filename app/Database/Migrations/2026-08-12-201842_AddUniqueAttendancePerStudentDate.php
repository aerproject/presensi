<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueAttendancePerStudentDate extends Migration
{
    public function up()
    {
        $this->db->query(
            'ALTER TABLE attendance
             ADD UNIQUE KEY uq_attendance_student_date
             (siswa_akademik_id, tanggal)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE attendance
             DROP INDEX uq_attendance_student_date'
        );
    }
}
