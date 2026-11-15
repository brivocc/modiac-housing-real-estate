<?php
// Include database configuration
include("config.php");

// Set content type to JSON
header('Content-Type: application/json');

// Check if state_id is provided
if (isset($_GET['state_id']) && is_numeric($_GET['state_id'])) {
    $state_id = intval($_GET['state_id']);
    
    // Query to get cities for the selected state
    $query = "SELECT cid, cname FROM city WHERE sid = $state_id ORDER BY cname ASC";
    $result = mysqli_query($con, $query);
    
    $cities = array();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cities[] = array(
                'cid' => $row['cid'],
                'cname' => $row['cname']
            );
        }
    }
    
    // Return JSON response
    echo json_encode($cities);
} else {
    // Return empty array if no state_id provided
    echo json_encode(array());
}
?>
