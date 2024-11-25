<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Profile extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('StudentModel');
        $this->load->model('TeacherModel');
        $this->load->model('ProfileModel');
        $this->load->model('LogModel');
    }
    public function index()
    {
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'profile';
        $teacherId = $data['teacher_id'];
        $studentId = $data['student_id'];
        if ($teacherId) {
            $teacher = $this->ProfileModel->getTeacherById($teacherId);
            if ($teacher)
            {
                $data['teacherName'] = htmlspecialchars($teacher->Teacher_Name);
                $data['teacherAge'] = htmlspecialchars($teacher->Teacher_Age);
                $data['teacherGender'] = htmlspecialchars($teacher->Teacher_Gender);
                $data['teacherContact'] = htmlspecialchars($teacher->Teacher_Contact);
                $data['salary'] = htmlspecialchars($teacher->Salary);
                $useracc = $this->ProfileModel->getUserAccByTeacherId($teacherId);
                if ($useracc) 
                {
                    $data['teacherPwd'] = htmlspecialchars($useracc->User_Pwd);
                    $data['teacherEmail'] = htmlspecialchars($useracc->User_Email);
                }
            }
        } elseif ($studentId) {
            $student = $this->ProfileModel->getStudentById($studentId);
            if ($student)
            {
                $data['studentName'] = htmlspecialchars( $student->Student_Name);
                $data['studentAge'] = htmlspecialchars( $student->Student_Age);
                $data['studentGender'] = htmlspecialchars( $student->Student_Gender);
                $data['studentContact'] = htmlspecialchars( $student->Student_Contact);
                $useracc = $this->ProfileModel->getUserAccByStudentId($studentId);
                if ($useracc)
                {
                    $data['studentPwd'] = htmlspecialchars($useracc->User_Pwd);
                    $data['studentEmail'] = htmlspecialchars( $useracc->User_Email);
                }
            }
            else
            {
                $data['error_message'] = 'Student details not found.';
            }
        } else {
            $data['error_message'] = 'No user is logged in.';
        }
        $this->load->view('header', $data);
        $this->load->view('profileView');
        $this->load->view('footer');
    }
    public function updateProfile() 
    {
        $ic = $this->input->post('ic');
        $name = $this->input->post('name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $phone = $this->input->post('phone' ,TRUE);
        $email = $this->input->post('email', TRUE);
        $salary = $this->input->post('salary', TRUE);
        $studentId = $this->session->userdata('student_id');
        $teacherId = $this->session->userdata('teacher_id');
        $CheckResult = $this->ProfileModel->checkDuplicationUser($email, $name, $ic);
        if ($CheckResult == null) 
        {
            if ($studentId) 
            {
                $this->StudentModel->updateStudent($ic, $name, $age, $gender, $phone);
                $this->ProfileModel->updateProfile($email, $name, $ic);
                $actResult = true;
            }
            elseif ($teacherId) 
            {
                $this->TeacherModel->updateTeacher($ic, $name, $age, $gender, $phone, $salary);
                $this->ProfileModel->updateProfile($email, $name, $ic);
                $actResult = true;
            }
        } else {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Duplicate email or username. Please try again.'
            ]);
            $actResult = false;
        }
        $logtype='Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
		if($actResult){	
			$logtype = "Info";
			$logmsg = 'User profile updated';
			$this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
		}
		else{
			$logtype = "Error";
			$logmsg = 'Failed to update user profile';
			$this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
		}
        redirect('profile');
    }
    public function verifyPwd() 
    {
        $currentPassword = $this->input->post('currentPassword');
        $passwordHolding = $this->input->post('passwordHolding');
        $isValid = $this->ProfileModel->verifyPassword($currentPassword, $passwordHolding);
        $response = ['success' => $isValid];
        echo json_encode( $response);
    }
    public function updatePwd()
    {
        $latestPassword = $this->input->post('confirmPassword');
        $userId = $this->input->post('UserID');
        $updateResult = $this->ProfileModel->update_user($latestPassword, $userId);
        redirect('userLogout');
    }
}

?>