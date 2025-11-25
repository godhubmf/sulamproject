<?php

class WarisAdminController {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function showUserWaris($userId) {
        // 1. Fetch User Details
        $stmt = $this->mysqli->prepare("SELECT id, name, username, email, phone_number FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $user = $userResult->fetch_assoc();

        if (!$user) {
            return ['error' => 'User not found'];
        }

        // 2. Fetch Waris List for this User
        $stmtWaris = $this->mysqli->prepare("SELECT * FROM next_of_kin WHERE user_id = ? ORDER BY created_at DESC");
        $stmtWaris->bind_param('i', $userId);
        $stmtWaris->execute();
        $warisResult = $stmtWaris->get_result();
        $warisList = $warisResult->fetch_all(MYSQLI_ASSOC);

        return [
            'targetUser' => $user,
            'warisList' => $warisList
        ];
    }
}
