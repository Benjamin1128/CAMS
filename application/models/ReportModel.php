<?php

class ReportModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getClasses()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->select('*');
        $this->db->from('classroom');
        if ($teacherId !== null) {
            $this->db->where('Teacher_ID', $teacherId);
        }
        $query = $this->db->get();
        if (!$query) {
            return false;
        } 
        $classrooms = $query->result_array();
        return $classrooms;
    }
    public function getStudents()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->distinct();
        $this->db->select('s.Student_Name, s.Student_ID');
        $this->db->from('student s');
        if ($teacherId !== null) {
            
        }
        $query = $this->db->get();
        if (!$query) {
            return false;
        }
        $students = $query->result_array();
        return $students;
    }
    public function getDistinctClassIds()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->distinct();
        $this->db->select('c.Class_ID');
        $this->db->from('course c');
        $this->db->join('classroom cl', 'c.Class_ID = cl.Class_ID');
        $this->db->where('cl.Teacher_ID', $teacherId);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getDistinctStudentIds()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->distinct();
        $this->db->select('c.Student_ID');
        $this->db->from('course c');
        $this->db->join('classroom cl','c.Class_ID = cl.Class_ID');
        $this->db->where('cl.Teacher_ID', $teacherId);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getAllReport($classIds, $studentIds, $startDate, $endDate)
    {
        $distinctClassResults = $this->getDistinctClassIds();
        $distinctStudentResults = $this->getDistinctStudentIds();
        $distinctClassIds = array_column($distinctClassResults,'Class_ID');
        $distinctStudentIds = array_column($distinctStudentResults,'Student_ID');
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->select('*');
        $this->db->from('attendance');
        if (!empty($classIds) && $classIds[0] !== 'all') {
            $this->db->where_in('Class_ID', $classIds);
        } else {
            if ($teacherId !== null) {
                $this->db->where_in('Class_ID', $distinctClassIds);
            }
        }
        if (!empty($studentIds) && $studentIds[0] !== 'all') {
            $this->db->where_in('Student_ID', $studentIds);
        } else {
            if ($teacherId !== null) {
                $this->db->where_in('Student_ID', $distinctStudentIds);
            }
        }
        if ($startDate !== null && $endDate !== null) {
            $this->db->where('Attendance_Date >=', $startDate);
            $this->db->where('Attendance_Date <=', $endDate);
        }
        $query = $this->db->get();
        if (!$query) {
            return false;
        }
        return $query->result_array();
    }
}

?>