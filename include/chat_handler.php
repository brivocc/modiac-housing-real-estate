<?php
// include/chat_handler.php
// Core chat functionality for sending and retrieving messages

require_once '../config.php'; // Database connection

class ChatHandler {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new conversation or get existing one
    public function getOrCreateConversation($user_id, $agent_id, $property_id = null, $subject = null) {
        $conversation_id = null;
        $sql = "SELECT id FROM conversations WHERE user_id = ? AND agent_id = ? AND property_id ";
        if ($property_id === null) {
            $sql .= "IS NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $agent_id);
        } else {
            $sql .= "= ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iii", $user_id, $agent_id, $property_id);
        }
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($found_id);
            $stmt->fetch();
            $conversation_id = $found_id;
            $stmt->close();
            return $conversation_id;
        }
        $stmt->close();

        // Create new conversation
        $sql = "INSERT INTO conversations (user_id, agent_id, property_id, subject) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiis", $user_id, $agent_id, $property_id, $subject);
        $stmt->execute();
        $conversation_id = $stmt->insert_id;
        $stmt->close();
        return $conversation_id;
    }

    // Send a message
    public function sendMessage($conversation_id, $sender_id, $message) {
        $sql = "INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $conversation_id, $sender_id, $message);
        $stmt->execute();
        $stmt->close();

        // Update conversation updated_at
        $sql = "UPDATE conversations SET updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversation_id);
        $stmt->execute();
        $stmt->close();
    }

    // Get messages for a conversation
    public function getMessages($conversation_id) {
        $sql = "SELECT m.id, m.sender_id, m.message, m.is_read, m.created_at, u.uname FROM messages m JOIN user u ON m.sender_id = u.uid WHERE m.conversation_id = ? ORDER BY m.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        $stmt->close();
        return $messages;
    }

    // Mark messages as read
    public function markMessagesRead($conversation_id, $user_id) {
        $sql = "UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $conversation_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>
