<?php

class ClassroomModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getClassroomById($id)
    {
        $id = (int)$id;
        $classroom = [];
        $students = [];
        $this->db->select('c.Class_Subject, c.Teacher_ID, Co.Student_ID, c.Class_StartTime, c.Class_EndTime');
        $this->db->from('classroom c');
        $this->db->join('course AS Co', 'c.Class_ID = Co.Class_ID', 'left');
        $this->db->where('c.Class_ID', $id);
        $this->db->where('Co.Subject_Status', 'A');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if (empty($classroom)) {
                    $classroom = [
                        'Class_Subject' => $row['Class_Subject'],
                        'Class_StartTime' => $row['Class_StartTime'],
                        'Class_EndTime' => $row['Class_EndTime'],
                        'Teacher_ID' => $row['Teacher_ID'],
                    ];   
                }
                $students[] = $row['Student_ID'];
            }
        }
        return [
            'classroom' => $classroom,
            'students' => $students,
        ];
    }
    public function getAllClassrooms() 
    {
        $teacherId	=$this->session->userdata('teacher_id');
		$studentId	=$this->session->userdata('student_id');
        $this->db->select('c.Class_ID, c.Class_Subject, t.Teacher_Name, COUNT(ts.Student_ID) AS Total_Student, c.Class_StartTime, c.Class_EndTime');
        $this->db->from('classroom AS c');
        $this->db->join('course AS ts','c.Class_ID = ts.Class_ID', 'left');
        $this->db->join('teacher AS t','c.Teacher_ID = t.Teacher_ID','left');
        $this->db->where('ts.Subject_Status', 'A');
        if ($teacherId !== null) {
            $this->db->where('c.Teacher_ID', $teacherId);
        } elseif ($studentId !== null) {
            $this->db->where('ts.Student_ID', $studentId);
        }
        $this->db->group_by('c.Class_ID, c.Class_Subject, t.Teacher_Name, c.Class_StartTime, c.Class_EndTime');
        $query = $this->db->get();
        $classrooms = $query->result_array();
        return $classrooms;
    }

    public function getTotalClassroom() 
    {
        $teacherId	=$this->session->userdata('teacher_id');
		$studentId	=$this->session->userdata('student_id');
        $this->db->select('COUNT(DISTINCT c.Class_ID) as TotalClassroom');
        if ($studentId !== null) {
            $this->db->from('course as cr');
            $this->db->join('classroom as c', 'cr.Class_ID = c.Class_ID');
            $this->db->where('cr.Student_ID', $studentId);
        } else {
            $this->db->from('classroom c');
            if ($teacherId !== null) {
                $this->db->where('c.Teacher_ID', $teacherId);
            }
        }
        $query = $this->db->get();
        $row = $query->row_array();
        return $row['TotalClassroom'];
    }       
    public function insertClassroom($subjectName, $teacherId, $studentsList, $classStartTime, $classEndTime)
    {
        $data = [
            'Class_Subject' => $subjectName,
            'Teacher_ID' => $teacherId,
            'Class_StartTime' => $classStartTime,
            'Class_EndTime' => $classEndTime,
        ];    
        $this->db->insert('classroom', $data);
        $classId = $this->db->insert_id();
        if (!empty($studentsList)) {
            $data = [];
            foreach ($studentsList as $studentId) {
                $data[] = [
                    'Student_ID' => $studentId,
                    'Class_ID' => $classId,
                ];
            }
            $this->db->insert_batch('course', $data);
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('message', [
                    'type' => 'success',
                    'text' => 'Classroom added successfully!',
                ]);
                return true;
            } else {
                $this->session->set_flashdata('message', [
                    'type' => 'error',
                    'text' => 'Failed to add classroom. Please try again.',
                ]);
                return false;
            }
        } else {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Classroom added successfully!',
            ]);
            return true;
        }
    }
    public function updateClassroom($subjectName, $teacherId, $studentsList, $classId, $classStartTime, $classEndTime) {
        $classId = (int) $classId;
        $this->db->set('Class_Subject', $subjectName);
        $this->db->set('Teacher_ID', $teacherId);
        $this->db->set('Class_StartTime', $classStartTime);
        $this->db->set('Class_EndTime', $classEndTime);
        $this->db->where('Class_ID', $classId);
        $updateResult = $this->db->update('classroom');
        
        if (!$updateResult) {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Failed to update classroom info. Please try again.']);
            return false;
        }

        $this->db->trans_start();
        $this->db->set('Subject_Status', 'D');
        $this->db->where('Class_ID', $classId);
        $this->db->update('course');
    
        foreach ($studentsList as $studentId) {
            $this->db->where('Class_ID', $classId);
            $this->db->where('Student_ID', $studentId);
            $query = $this->db->get('course');
            if ($query->num_rows() > 0) {
                $this->db->set('Subject_Status', 'A');
                $this->db->where('Class_ID', $classId);
                $this->db->where('Student_ID', $studentId);
                $this->db->update('course');
            } else {
                $data = [
                    'Class_ID' => $classId,
                    'Student_ID' => $studentId,
                    'Subject_Status' => 'A'
                ];
                $this->db->insert('course', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Failed to update classroom info. Please try again.']);
            return false;
        } else {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Classroom info updated successfully!']);
            return true;
        }
    }
    public function removeClassroom($id) 
    {
        try {
            $this->db->where('Class_ID', $id);
            $deleteResult = $this->db->delete('classroom');
            if (!$deleteResult) 
            {
                $this->session->set_flashdata('message', ['type'=> 'error', 'text'=> 'Failed to remove classroom. Please try again.']);
                return false;
            } 
            else 
            {
                $this->session->set_flashdata('message', ['type'=> 'success','text' => 'Classroom removed successfully!']);
                return true;
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
