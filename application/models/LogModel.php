<?php

class LogModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getUserName($acid)
    {
        $this->db->select('User_ID');
        $this->db->from('useracc');
        $this->db->where('Acc_ID', $acid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['User_ID'];
        } else {
            return null;
        }
    }
    public function getAvailableUser()
    {
        $query = $this->db->get('useracc');
        if ($query->num_rows() > 0) {
            $users = $query->result_array();
        } else {
            $users = [];
        }
        return $users;
    }
    public function writeDownLog($logtype, $logmsg, $loguser) 
    {
        $timezone = new DateTimeZone('Asia/Kuala_Lumpur');
        $currentDateTime = (new DateTime('now', $timezone))->format('Y-m-d H:i:s');
        $data = [
            'AcLog_Comment' => $logmsg,
            'AcLog_DateTime' => $currentDateTime,
            'Acc_ID' => $loguser,
            'AcLog_Type' => $logtype,
        ];
        $this->db->insert('actionlog', $data);
    }
    public function getLog($userid, $logtype, $startDate, $endDate)
    {
        $this->db->select('acl.AcLog_Comment, acl.AcLog_DateTime, acl.Acc_ID, acl.AcLog_Type, u.User_ID');
        $this->db->from('actionlog acl');
        $this->db->join('useracc u', 'acl.Acc_ID = u.Acc_ID', 'left');
        if ($userid !== 'all') {
            $this->db->where('acl.Acc_ID', $userid);
        }
        if ($logtype !== 'all') {
            $this->db->where('acl.AcLog_Type', $logtype);
        }
        if ($startDate !== null && $endDate !== null) {
            $startDate .= '00:00:00';
            $endDate .= '23:59:59';
            $this->db->where('acl.AcLog_DateTime >=', $startDate);
            $this->db->where('acl.AcLog_DateTime <=', $endDate);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
}

?>