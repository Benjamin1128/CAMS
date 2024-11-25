<?php

class AttendanceModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_student_by_class_id($classId)
    {
        $this->db->select('
            c.Course_ID,
            s.Student_ID,
            s.Student_Name,
            s.Student_Contact,
            a.Student_Status AS Attendance_Status
        ');
        $this->db->from('course c');
        $this->db->join('student s', 'c.Student_ID = s.Student_ID');
        $this->db->join('attendance a', 'a.Student_ID = s.Student_ID AND a.class_ID = c.Class_ID  AND a.Attendance_Date = CURDATE()', 'left');
        $this->db->where('c.Class_ID', $classId);
        $this->db->where('c.Subject_Status', 'A');
        $this->db->order_by("
            CASE
                WHEN a.Student_Status IS NULL THEN 1
                WHEN a.Student_Status = 'Late' THEN 2
                WHEN a.Student_Status = 'Absent' THEN 3
                WHEN a.Student_Status = 'Present' THEN 4
                ELSE 5
            END
        ", '', false);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function get_class_and_teacher_details($classId)
    {
        $this->db->select('cl.Class_Subject, t.Teacher_Name');
        $this->db->from('classroom cl');
        $this->db->join('teacher t', 'cl.Teacher_ID = t.Teacher_ID');
        $this->db->where('cl.Class_ID', $classId);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_student_without_attendance_record($classId)
    {
        $this->db->select('s.Student_ID, s.Student_Name');
        $this->db->from('course c');
        $this->db->join('student s', 'c.Student_ID = s.Student_ID', 'inner');
        $this->db->join('attendance a', 'a.Student_ID = s.Student_ID AND a.Class_ID = c.Class_ID AND a.Attendance_Date = CURDATE()', 'left');
        $this->db->where('c.Class_ID', $classId);
        $this->db->where('c.Subject_Status', 'A');
        $this->db->where('a.Student_ID IS NULL');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getStudentAttendanceStatus($StudId, $classId) 
    {
        $this->db->select('Student_Status');
        $this->db->from('attendance');
        $this->db->where('Student_ID', $StudId);
        $this->db->where('Class_ID', $classId);
        $this->db->where('Attendance_Date', 'CURDATE()', false);
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['Student_Status']) ? $result['Student_Status'] : 'Not Attended';
    }
    public function insertAttendance($courseId, $studentStatus)
    {
        $this->db->select('Class_ID, Student_ID');
        $this->db->from('course');
        $this->db->where('Course_ID', $courseId);
        $query = $this->db->get();
        $courseData = $query->row_array();
        if ($courseData) {
            $classId = $courseData['Class_ID'];
            $studentId = $courseData['Student_ID'];
            $todayDate = date('Y-m-d');
            $data = array(
                'Student_ID' => $studentId,
                'Class_ID' => $classId,
                'Attendance_Date' => $todayDate,
                'Student_Status' => $studentStatus,
            );
            $this->db->where('Student_ID', $studentId);
            $this->db->where('Class_ID', $classId);
            $this->db->where('Attendance_Date', $todayDate);
            if ($this->db->count_all_results('attendance') > 0)
            {
                $this->db->set('Student_Status', $studentStatus);
                $this->db->where('Student_ID', $studentId);
                $this->db->where('Class_ID', $classId);
                $this->db->where('Attendance_Date', $todayDate);
                $this->db->update('attendance');
            } else {
                $this->db->insert('attendance', $data);
            }
            return $this->db->affected_rows() > 0;
        } else {
            return false;
        }
    }
    public function takeAllAttendance($classId, $studentStatus)
    {
        $this->db->select('Student_ID');
        $this->db->from('course');
        $this->db->where('Class_ID', $classId);
        $this->db->where('Subject_Status', 'A');
        $query = $this->db->get();
        $studentIds= $query->result_array();
        $todayDate = date('Y-m-d');
        $this->db->trans_start();
        foreach ($studentIds as $student) {
            $studentId = $student['Student_ID'];
            
            $this->db->select('Attendance_ID');
            $this->db->from('attendance');
            $this->db->where('Student_ID', $studentId);
            $this->db->where('Class_ID', $classId);
            $this->db->where('Attendance_Date', $todayDate);
            $existingRecord = $this->db->get()->row_array();

            if ($existingRecord) {
                $data = array(
                    'Student_Status' => $studentStatus,
                );
                $this->db->where('Student_ID', $studentId);
                $this->db->where('Class_ID', $classId);
                $this->db->where('Attendance_Date', $todayDate);
                $this->db->update('attendance', $data);
            } else {
                $data = array(
                    'Student_ID' => $studentId,
                    'Class_ID' => $classId,
                    'Attendance_Date' => $todayDate,
                    'Student_Status' => $studentStatus,
                );
                $this->db->insert('attendance', $data);
            }
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    public function getStudentWithoutAttendRePast($classId, $pastDate)
    {
        $this->db->select('s.Student_ID, s.Student_Name');
        $this->db->from('course c');
        $this->db->join('student s', 'c.Student_ID = s.Student_ID', 'inner');
        $this->db->join('attendance a', 'a.Student_ID = s.Student_ID AND a.Class_ID = c.Class_ID AND a.Attendance_Date = ' . $this->db->escape($pastDate), 'left');
        $this->db->where('c.Class_ID', $classId);
        $this->db->where('c.Subject_Status', 'A');
        $this->db->where('a.Student_ID IS NULL');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getPastStudentsByClassId($pastDate, $classId)
    {
        $this->db->select('
            c.Course_ID,
            s.Student_ID,
            s.Student_Name,
            s.Student_Contact,
            a.Student_Status AS Attendance_Status
        ');
        $this->db->from('course c');
        $this->db->join('student s', 'c.Student_ID = s.Student_ID');
        $this->db->join('attendance a', 'a.Student_ID = s.Student_ID AND a.class_ID = c.Class_ID  AND a.Attendance_Date = ' . $this->db->escape($pastDate), 'left');
        $this->db->where('c.Class_ID', $classId);
        $this->db->where('c.Subject_Status', 'A');
        $this->db->order_by("
            CASE
                WHEN a.Student_Status IS NULL THEN 1
                WHEN a.Student_Status = 'Late' THEN 2
                WHEN a.Student_Status = 'Absent' THEN 3
                WHEN a.Student_Status = 'Present' THEN 4
                ELSE 5
            END
        ", '', false);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function insertPastAttendance($courseId, $studentStatus, $pastDate)
    {
        $this->db->select('Class_ID, Student_ID');
        $this->db->from('course');
        $this->db->where('Course_ID', $courseId);
        $query = $this->db->get();
        $courseData = $query->row_array();
        if ($courseData) {
            $classId = $courseData['Class_ID'];
            $studentId = $courseData['Student_ID'];
            $data = array(
                'Student_ID' => $studentId,
                'Class_ID' => $classId,
                'Attendance_Date' => $pastDate,
                'Student_Status' => $studentStatus,
            );
            $this->db->where('Student_ID', $studentId);
            $this->db->where('Class_ID', $classId);
            $this->db->where('Attendance_Date', $pastDate);
            if ($this->db->count_all_results('attendance') > 0)
            {
                $this->db->set('Student_Status', $studentStatus);
                $this->db->where('Student_ID', $studentId);
                $this->db->where('Class_ID', $classId);
                $this->db->where('Attendance_Date', $pastDate);
                $this->db->update('attendance');
            } else {
                $this->db->insert('attendance', $data);
            }
            return $this->db->affected_rows() > 0;
        } else {
            return false;
        }
    }
    public function takeAllPastAttendance($classId, $studentStatus, $pastDate)
    {
        $this->db->select('Student_ID');
        $this->db->from('course');
        $this->db->where('Class_ID', $classId);
        $this->db->where('Subject_Status', 'A');
        $query = $this->db->get();
        $studentIds= $query->result_array();
        $this->db->trans_start();
        foreach ($studentIds as $student) {
            $studentId = $student['Student_ID'];
    
            $this->db->select('Attendance_ID');
            $this->db->from('attendance');
            $this->db->where('Student_ID', $studentId);
            $this->db->where('Class_ID', $classId);
            $this->db->where('Attendance_Date', $pastDate);
            $existingRecord = $this->db->get()->row_array();
            
            if ($existingRecord) {

                $data = array(
                    'Student_Status' => $studentStatus,
                );
    
                $this->db->where('Student_ID', $studentId);
                $this->db->where('Class_ID', $classId);
                $this->db->where('Attendance_Date', $pastDate);
                $this->db->update('attendance', $data);
            } else {
                $data = array (
                    'Student_ID' => $studentId,
                    'Class_ID' => $classId,
                    'Attendance_Date' => $pastDate,
                    'Student_Status' =>$studentStatus,
                );
                $this->db->insert('attendance', $data);
            }
    
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    public function CheckClassTime($classroom)
    {
        if (!$classroom) 
        {
            return 'Class not found';
        }
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $classStartTime = new DateTime($classroom['Class_StartTime']);
        $classEndTime = new DateTime($classroom['Class_EndTime']);
        $currentTime = new DateTime();
        $earlyOrLateInterval = new DateInterval('PT10M');
        $lateTime = clone $classStartTime;
        $lateTime->add($earlyOrLateInterval);
        $earlyTime = clone $classStartTime;
        $earlyTime->sub($earlyOrLateInterval);
        if ($currentTime >= $earlyTime && $currentTime <= $lateTime) {
            return 'Present';
        } else if ($currentTime > $lateTime && $currentTime <= $classEndTime) {
            return 'Late';
        } else if ($currentTime > $classEndTime) {
            return 'Absent';
        }
    }
    public function StudentInsertAttendance($classId, $studentId, $studentStatus) 
    {
        $todayDate = date('Y-m-d');
        $data = array(
            'Student_ID' => $studentId,
            'Class_ID' => $classId,
            'Attendance_Date' => $todayDate,
            'Student_Status' => $studentStatus,
        );
        $this->db->insert('attendance', $data);
        return $this->db->affected_rows() > 0;  
    }
}
