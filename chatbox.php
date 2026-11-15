<?php
// chatbox.php - Main chat interface for agent messaging system
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['uid'];
$agent_id = isset($_GET['agent_id']) ? intval($_GET['agent_id']) : 0;
$property_id = isset($_GET['property_id']) ? intval($_GET['property_id']) : 0;

// Check if agent exists
$agent_check = $con->prepare("SELECT uid, uname FROM user WHERE uid = ? AND utype = 'agent'");
$agent_check->bind_param("i", $agent_id);
$agent_check->execute();
$agent_result = $agent_check->get_result();
if ($agent_result->num_rows == 0) {
    header("Location: agent.php");
    exit();
}
$agent = $agent_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($agent['uname']); ?></title>
    <link href="css/bootstrap.min.css" rel="The errors are caused by the variable $found_id being used before assignment in the bind_result and fetch block. I will fix this by declaring $found_id before binding and fetching.

Here is the corrected edit:

<edit_file>
<path>include/chat_handler.php</path>
<content>
<<<<<<< SEARCH
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($found_id);
            $stmt->fetch();
            $conversation_id = $found_id;
            $stmt->close();
            return $conversation_id;
        }
=======
        if ($stmt->num_rows > 0) {
            $found_id = null;
            $stmt->bind_result($found_id);
            $stmt->fetch();
            $conversation_id = $found_id;
            $stmt->close();
            return $conversation_id;
        }
