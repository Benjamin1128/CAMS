<?php

class ProfileModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_user_by_username($username) 
    {
        $this->db->select("Acc_ID, User_Pwd, Teacher_ID, Student_ID");
        $this->db->where('User_ID', $username);
        $query = $this->db->get('useracc');
        return $query->row_array();
    }
    public function checkDuplicationUser($useremail, $username, $userId)
    {
        $this->db->where('(User_Email = ' . $this->db->escape($useremail) . ' OR User_ID = ' . $this->db->escape($username) . ')');
        $this->db->where('(Teacher_ID != ' . $this->db->escape($userId) . ' OR Student_ID != ' . $this->db->escape($userId) . ')');
        $query = $this->db->get('useracc');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        } else {
            return null;
        }
    }
    public function createStudentAcc($ic, $name, $email)
    {
        $plain_password = substr($ic, -6);
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        $data = [
            'User_ID' => $name,
            'User_Pwd' => $hashed_password,
            'Student_ID' => $ic,
            'User_Email' => $email,
        ];
        try {
            $insertResult = $this->db->insert('useracc', $data);
            if ($insertResult) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    public function createTeacherAcc($ic, $name, $email)
    {
        $plain_password = substr($ic, -6);
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        $data = [
            'User_ID' => $name,
            'User_Pwd' => $hashed_password,
            'Teacher_ID' => $ic,
            'User_Email' => $email,
        ];
        try {
            $insertResult = $this->db->insert('useracc', $data);
            if ($insertResult) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    public function getTeacherById($teacherId)
    {
        $this->db->where('Teacher_ID', $teacherId);
        $query = $this->db->get('teacher');
        if ($query->num_rows() == 1) {
            return $query->row();
        }
        return null;
    }
    public function getStudentById($studentId)
    {
        $this->db->where('Student_ID', $studentId);
        $query = $this->db->get('student');
        if ($query->num_rows() == 1) {
            return $query->row();
        }
        return null;
    }
    public function getUserAccByStudentId($studentId)
    {
        $this->db->select('User_Pwd, User_Email');
        $this->db->where('Student_ID', $studentId);
        $query = $this->db->get('useracc');
        if ($query->num_rows() == 1) {
            return $query->row();
        }
        return null;
    }
    public function getUserAccByTeacherId($teacherId)
    {
        $this->db->select('User_Pwd, User_Email');
        $this->db->where('Teacher_ID', $teacherId);
        $query = $this->db->get('useracc');
        if ($query->num_rows() == 1) {
            return $query->row();
        }
        return null;
    }
    public function updateProfile($useremail, $username, $targetId) 
    {
        $data = [
            'User_Email' => $useremail,
            'User_ID' => $username,
        ];
        $this->db->group_start();
        $this->db->where('Teacher_ID', $targetId);
        $this->db->or_where('Student_ID', $targetId);
        $this->db->group_end();
        $updateResult = $this->db->update('useracc', $data);
        if ($updateResult)
        {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'User Profile updated successfully!',
            ]);
            return true;
        }
        else
        {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Failed to update user profile. Please try again.',
            ]);
            return false;
        }
    }
    public function verifyPassword($currentPassword, $hashedPassword)
    {
        return password_verify($currentPassword, $hashedPassword);
    }
    public function update_user($userPwd, $targetId) 
    {
        $hashed_password = password_hash($userPwd, PASSWORD_DEFAULT);
        $data = array(
            'User_Pwd' => $hashed_password,
        );
        $this->db->group_start();
        $this->db->where('Teacher_ID', $targetId);
        $this->db->or_where('Student_ID', $targetId);
        $this->db->group_end();
        $this->db->update('useracc', $data);
        if ($this->db->affected_rows() > 0) 
        {
            return true;
        } 
        else {
            return false;
        }
    }
}
