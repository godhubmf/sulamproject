<?php

class UsersModel {
    private $db;

    public function __construct($mysqli) {
        $this->db = $mysqli;
    }

    public function getUsers($role = null) {
        $sql = "SELECT * FROM users";
        if ($role) {
            $sql .= " WHERE roles = ?";
        }
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($role) {
            $stmt->bind_param('s', $role);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
