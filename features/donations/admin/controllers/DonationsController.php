<?php

class DonationsController {
    private $mysqli;
    private $uploadDir;

    public function __construct($mysqli, $rootPath) {
        $this->mysqli = $mysqli;
        $this->uploadDir = $rootPath . '/assets/uploads';
    }

    public function handleCreate() {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            $gamba = null;
            // Handle file upload if provided
            if (!empty($_FILES['gamba']['name'])) {
                if (!is_dir($this->uploadDir)) { @mkdir($this->uploadDir, 0777, true); }
                $ext = pathinfo($_FILES['gamba']['name'], PATHINFO_EXTENSION);
                $basename = 'donation_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext?'.'.preg_replace('/[^a-zA-Z0-9]+/','',$ext):'');
                $target = $this->uploadDir . '/' . $basename;
                if (move_uploaded_file($_FILES['gamba']['tmp_name'], $target)) {
                    $gamba = 'assets/uploads/' . $basename;
                }
            } else if (isset($_POST['gamba_url']) && $_POST['gamba_url'] !== '') {
                $gamba = trim($_POST['gamba_url']);
            }
            
            if ($title === '') { 
                $message = 'Title is required.'; 
                $messageClass = 'notice error';
            } else {
                $stmt = $this->mysqli->prepare('INSERT INTO donations (title, description, image_path, is_active) VALUES (?, ?, ?, ?)');
                if ($stmt) { 
                    $stmt->bind_param('sssi', $title, $desc, $gamba, $isActive); 
                    $stmt->execute(); 
                    $stmt->close(); 
                    $message = 'Donation post created'; 
                    $messageClass = 'notice success'; 
                } else {
                    $message = 'Database error: ' . $this->mysqli->error;
                    $messageClass = 'notice error';
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    public function getAllDonations() {
        $items = [];
        $res = $this->mysqli->query('SELECT id, title, description, image_path, is_active, created_at FROM donations ORDER BY id DESC');
        if ($res) { 
            while ($row = $res->fetch_assoc()) { 
                $items[] = $row; 
            } 
            $res->close(); 
        }
        return $items;
    }

    public function handleUpdate($id) {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $gamba = null;

            if (!empty($_FILES['gamba']['name'])) {
                if (!is_dir($this->uploadDir)) { @mkdir($this->uploadDir, 0777, true); }
                $ext = pathinfo($_FILES['gamba']['name'], PATHINFO_EXTENSION);
                $basename = 'donation_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext?'.'.preg_replace('/[^a-zA-Z0-9]+/','',$ext):'');
                $target = $this->uploadDir . '/' . $basename;
                if (move_uploaded_file($_FILES['gamba']['tmp_name'], $target)) {
                    $gamba = 'assets/uploads/' . $basename;
                }
            } else if (isset($_POST['gamba_url']) && $_POST['gamba_url'] !== '') {
                $gamba = trim($_POST['gamba_url']);
            }

            if ($title === '') {
                $message = 'Title is required.';
                $messageClass = 'notice error';
            } else {
                if ($gamba !== null) {
                    $stmt = $this->mysqli->prepare('UPDATE donations SET title=?, description=?, image_path=?, is_active=? WHERE id=?');
                    if ($stmt) {
                        $stmt->bind_param('sssii', $title, $desc, $gamba, $isActive, $id);
                        $stmt->execute();
                        $stmt->close();
                        $message = 'Donation updated successfully';
                        $messageClass = 'notice success';
                    } else {
                        $message = 'Database error: ' . $this->mysqli->error;
                        $messageClass = 'notice error';
                    }
                } else {
                    $stmt = $this->mysqli->prepare('UPDATE donations SET title=?, description=?, is_active=? WHERE id=?');
                    if ($stmt) {
                        $stmt->bind_param('ssii', $title, $desc, $isActive, $id);
                        $stmt->execute();
                        $stmt->close();
                        $message = 'Donation updated successfully';
                        $messageClass = 'notice success';
                    } else {
                        $message = 'Database error: ' . $this->mysqli->error;
                        $messageClass = 'notice error';
                    }
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    public function handleDelete($id) {
        $message = '';
        $messageClass = 'notice';
        $stmt = $this->mysqli->prepare('DELETE FROM donations WHERE id=?');
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $message = 'Donation deleted.';
            $messageClass = 'notice success';
        } else {
            $message = 'Database error: ' . $this->mysqli->error;
            $messageClass = 'notice error';
        }
        return ['message' => $message, 'messageClass' => $messageClass];
    }
}
