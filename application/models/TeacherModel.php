<?php 

class TeacherModel extends CI_Model 
{
    public function __construct() {
        parent::__construct();
    }

    public function getTeachers($start, $length, $search = null, $order_column = 0, $order_dir = 'asc')
    {
        $columns = [
            't.Teacher_ID',
            't.Teacher_Name',
            't.Teacher_Age',
            't.Teacher_Gender',
            't.Teacher_Contact',
            't.Salary',
            'NumberOfClasses'
        ];

        $this->db->select('
            t.Teacher_ID,
            t.Teacher_Name,
            t.Teacher_Age,
            t.Teacher_Gender,
            t.Teacher_Contact,
            t.Salary,
            COUNT(c.Class_ID) AS NumberOfClasses
        ');
        $this->db->from('teacher t');
        $this->db->join('classroom c', 't.Teacher_ID = c.Teacher_ID', 'left');
        $this->db->group_by([
            't.Teacher_ID',
            't.Teacher_Name',
            't.Teacher_Age',
            't.Teacher_Gender',
            't.Teacher_Contact',
            't.Salary'
        ]);

        // Add search functionality
        if ($search) {
            $this->db->group_start(); // Start grouping for OR condition
            $this->db->like('t.Teacher_Name', $search);
            $this->db->or_like('t.Teacher_ID', $search);
            $this->db->or_like('t.Teacher_Contact', $search);
            $this->db->group_end();
        }

        // Add ordering
        $this->db->order_by($columns[$order_column], $order_dir);

        // Add pagination
        $this->db->limit($length, $start);

        $query = $this->db->get();
        return $query->result_array();
    }


    public function getFilteredTeachersCount($search = null)
    {
        $this->db->select('COUNT(DISTINCT t.Teacher_ID) as total');
        $this->db->from('teacher t');
        $this->db->join('classroom c', 't.Teacher_ID = c.Teacher_ID', 'left');

        // Add search functionality
        if ($search) {
            $this->db->group_start(); // Start grouping for OR condition
            $this->db->like('t.Teacher_Name', $search);
            $this->db->or_like('t.Teacher_ID', $search);
            $this->db->or_like('t.Teacher_Contact', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        $row = $query->row_array();
        return $row['total'];
    }


    public function getTeacherById($teacherID) 
    {
        $this->db->select('*');
        $this->db->from('teacher');
        $this->db->where('Teacher_ID', $teacherID);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return false;
        }
    }
    public function getTotalTeachers() {		
		$studentId = $this->session->userdata('student_id');
		if ($studentId === null) {
			$this->db->select('COUNT(*) as TeacherAmount');
			$this->db->from('teacher');
		} else {
			$this->db->select('COUNT(DISTINCT t.Teacher_ID) as TeacherAmount');
			$this->db->from('teacher AS t');
			$this->db->join('classroom AS c', 't.Teacher_ID = c.Teacher_ID');
			$this->db->join('course AS cr', 'c.Class_ID = cr.Class_ID');
			$this->db->where('cr.Student_ID', $studentId);
		}
		$query = $this->db->get();
		$row = $query->row_array();
		return $row['TeacherAmount'];
    }
    public function getTotalSalary()
    {
        $this->db->select_sum('Salary', 'TotalSalary');
        $this->db->from('teacher');
        $query = $this->db->get();
        $row = $query ->row_array();
        return $row['TotalSalary'];
    }
    public function getTeacherWithClassCount()
    {
        $teacherId = $this->session->userdata('teacher_id');
        $this->db->select('t.Teacher_ID, t.Teacher_Name, COUNT(c.Class_ID) AS NumberOfClasses');
        $this->db->from('teacher t');
        $this->db->join('classroom c', 't.Teacher_ID = c.Teacher_ID', 'left');
        if ($teacherId !== null)
        {
            $this->db->where('t.Teacher_ID', $teacherId);
        }
        $this->db->group_by('t.Teacher_ID, t.Teacher_Name');
        $query = $this->db->get();
        $teachers = $query->result_array();
        return $teachers;
    }

    public function getAllTeachers()
    {
        $this->db->select('
            t.Teacher_ID,
            t.Teacher_Name,
            t.Teacher_Age,
            t.Teacher_Gender,
            t.Teacher_Contact,
            t.Salary,
            COUNT(c.Class_ID) AS NumberOfClasses,
        ');
        $this->db->from('teacher t');
        $this->db->join('classroom c','t.Teacher_ID = c.Teacher_ID','left');
        $this->db->group_by('
            t.Teacher_ID,
            t.Teacher_Name,
            t.Teacher_Age,
            t.Teacher_Gender,
            t.Teacher_Contact,
            t.Salary,
        ');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $teachers = $query->result_array();
        } 
        else
        {
            $teachers = [];
        }
        return $teachers;
    }

    public function insertTeacher($ic, $name, $age, $gender, $phone, $salary)
    {
        $this->db->where('Teacher_ID', $ic);
        $query = $this->db->get('teacher');
        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Failed to add teacher. A teacher with this ID already exists.'
            ]);
            return false;
        } else {
            $data = [
                'Teacher_ID' => $ic,
                'Teacher_Name' => $name,
                'Teacher_Age'=> $age,
                'Teacher_Gender' => $gender,
                'Teacher_Contact' => $phone,
                'Salary' => $salary,   
            ];
            $insertResult = $this->db->insert('teacher', $data);
            if ($insertResult) {
                $this->session->set_flashdata('message', [
                    'type' => 'success',
                    'text' => 'Teacher added successfully!',
                ]);
                return true;
            } else {
                $this->session->set_flashdata('message', [
                    'type' => 'error',
                    'text' => 'Failed to add teacher. Please try again.'
                ]);
                return false;
            }
        }
        
    }
    public function updateTeacher($ic, $name, $age, $gender, $phone, $salary)
    {
        $data = [
            'Teacher_Name' => $name,
            'Teacher_Age'=> $age,
            'Teacher_Gender' => $gender,
            'Teacher_Contact' => $phone,
            'Salary' => $salary,   
        ];
        $this->db->where('Teacher_ID', $ic);
        $updateResult = $this->db->update('teacher', $data);
        if ($updateResult)
        {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Teacher info updated successfully!',
            ]);
            return true;
        } else {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Failed to update teacher info. Please try again.'
            ]);
            return false;
        }
    }
    public function removeTeacher($id) 
        {
            $this->db->where('Teacher_ID', $id);
            $result = $this->db->delete('teacher');
            if ($result) {
                $this->session->set_flashdata('message', [
                    'type' => 'success',
                    'text' => 'Teacher removed successfully!',
                ]);
                return true;
            } else {
                $this->session->set_flashdata('message', [
                    'type' => 'error',
                    'text' => 'Failed to remove teacher. Please try again.'
                ]);
                return false;
            }
        }
}

?>