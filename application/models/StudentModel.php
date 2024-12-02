<?php 

class StudentModel extends CI_Model 
{
    public function __construct() {
        parent::__construct();
    }
    public function insertStudent($ic, $name, $age, $gender, $phone)
    {
        $this->db->where("Student_ID", $ic);
        $query = $this->db->get("student");

        if ($query->num_rows() > 0) {
            $this->session->flashdata('message', [
                'type' => 'error',
                'text' => 'Failed to add student. A student with this ID already exists.'
            ]);
            return false;
        } else {
            $data = [
                'Student_ID' => $ic,
                'Student_Name' => $name,
                'Student_Age' => $age,
                'Student_Gender' => $gender,
                'Student_Contact' => $phone,
            ];
            if ($this->db->insert('student', $data)) 
            {
                $this->session->set_flashdata('message', [
                    'type' => 'success',
                    'text' => 'Student added successfully!',
                ]);
                return true;
            } 
            else 
            {
                $this->session->set_flashdata('message', [
                    'type' => 'error',
                    'text' => 'Failed to add student. Please try again.'
                ]);
                return false;
            }
        }
    }

    public function getStudents($start, $length, $search = null, $order_column = 0, $order_dir = 'asc')
    {
        $teacherId = $this->session->userdata('teacher_id');

        $columns = [
            'Student_ID',
            'Student_Name',
            'Student_Age',
            'Student_Gender',
            'Student_Contact'
        ];
        // Build query with optional search
        $this->db->select('s.*');
        $this->db->from('student s');

        if ($teacherId !== null) {
            $this->db->join('course c', 's.Student_ID = c.Student_ID', 'left');
            $this->db->join('classroom l', 'c.Class_ID = l.Class_ID', 'left');
            $this->db->where('l.Teacher_ID', $teacherId);
            $this->db->where('c.Subject_Status', 'A');
        }

        if ($search) {
            $this->db->like('s.Student_Name', $search);
            $this->db->or_like('s.Student_ID', $search);
        }

        $this->db->order_by($columns[$order_column], $order_dir);

        $this->db->limit($length, $start);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getFilteredStudentsCount($search = null)
    {
        $teacherId = $this->session->userdata('teacher_id');

        $this->db->select('COUNT(DISTINCT s.Student_ID) as total');
        $this->db->from('student s');

        if ($teacherId !== null) {
            $this->db->join('course c', 's.Student_ID = c.Student_ID', 'left');
            $this->db->join('classroom l', 'c.Class_ID = l.Class_ID', 'left');
            $this->db->where('l.Teacher_ID', $teacherId);
            $this->db->where('c.Subject_Status', 'A');
        }

        if ($search) {
            $this->db->like('s.Student_Name', $search);
            $this->db->or_like('s.Student_ID', $search);
        }

        $query = $this->db->get();
        $row = $query->row_array();
        return $row['total'];
    }

    public function getTotalStudents()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->select('COUNT(DISTINCT s.Student_ID) as total');
        $this->db->from('student s');
        $this->db->join('course c', 's.Student_ID = c.Student_ID', 'left');
        $this->db->join('classroom l', 'c.Class_ID = l.Class_ID', 'left');
        if ($teacherId !== null) {
            $this->db->where('l.Teacher_ID', $teacherId);
            $this->db->where('c.Subject_Status', 'A');
        }
        $query = $this->db->get();
        $row = $query->row_array();
        return $row['total'];        
    }
    public function getStudentWithClassCount()
    {
        $this->db->select('s.Student_ID, s.Student_Name, COUNT(c.Course_ID) AS NumberOfCourses');
        $this->db->from('student s');
        $this->db->join('course c', 's.Student_ID = c.Student_ID', 'left');
        $this->db->group_by('s.Student_ID, s.Student_Name');
        $query = $this->db->get();
        $students = $query->result_array();
        return $students;
    }
    public function getAllStudents()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->distinct();
        $this->db->select('s.*');
        $this->db->from('student s');
        if ($teacherId !== null) {
            $this->db->join('course c', 's.Student_ID = c.Student_ID', 'left');
            $this->db->join('classroom l', 'c.Class_ID = l.Class_ID', 'left');
            $this->db->where('l.Teacher_ID', $teacherId);
            $this->db->where('c.Subject_Status', 'A');
        }

        $query = $this->db->get();
        $students = $query->result_array();
        return $students;
    }
    public function getStudentById($ic)
    {   
        $this->db->where('Student_ID', $ic);
        $query = $this->db->get('student');
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        } 
        else
        {
            return null;
        }
    }
    public function updateStudent($ic, $name, $age, $gender, $phone)
    {
        $data = [
            'Student_Name' => $name,
            'Student_Age' => $age,
            'Student_Gender' => $gender,
            'Student_Contact' => $phone,
        ];
        $this->db->where('Student_ID', $ic);
        $result = $this->db->update('student', $data);
        if ($result)
        {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Student info updated successfully!',
            ]);
            return true;
        }
        else
        {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Failed to update student info. Please try again.',
            ]);
            return false;
        }
    }

    public function removeStudent($id) 
    {
        $this->db->trans_start();

        // Delete from 'student' table
        $this->db->where('Student_ID', $id);
        $this->db->delete('student');
    
        // Delete from 'useracc' table
        $this->db->where('Student_ID', $id);
        $this->db->delete('useracc');
    
        // Complete the transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) 
        {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Student removed successfully!'
            ]);
            return true;                    
        } 
        else
        {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Failed to remove student. Please try again.'
            ]);
            return false;
        }
    }
}

?>