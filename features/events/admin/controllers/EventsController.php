<?php

class EventsController {
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
            $date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
            $time = !empty($_POST['event_time']) ? $_POST['event_time'] : null;
            $location = trim($_POST['location'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            $gamba = null;
            // Handle file upload if provided
            if (!empty($_FILES['gamba']['name'])) {
                if (!is_dir($this->uploadDir)) { @mkdir($this->uploadDir, 0777, true); }
                $ext = pathinfo($_FILES['gamba']['name'], PATHINFO_EXTENSION);
                $basename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext?'.'.preg_replace('/[^a-zA-Z0-9]+/','',$ext):'');
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
                $stmt = $this->mysqli->prepare('INSERT INTO events (title, description, event_date, event_time, location, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
                if ($stmt) { 
                    $stmt->bind_param('ssssssi', $title, $desc, $date, $time, $location, $gamba, $isActive); 
                    $stmt->execute(); 
                    $stmt->close(); 
                    $message = 'Event created successfully'; 
                    $messageClass = 'notice success'; 
                } else {
                    $message = 'Database error: ' . $this->mysqli->error;
                    $messageClass = 'notice error';
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    public function getAllEvents() {
        $items = [];
        $res = $this->mysqli->query('SELECT id, title, description, event_date, event_time, location, image_path, is_active, created_at FROM events ORDER BY event_date DESC, id DESC');
        if ($res) { 
            while ($row = $res->fetch_assoc()) { 
                $items[] = $row; 
            } 
            $res->close(); 
        }
        return $items;
    }
}
