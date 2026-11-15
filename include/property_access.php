<?php
/**
 * Property Access Control Functions
 * Provides ownership verification and access control for property editing
 */

/**
 * Check if current user owns a specific property
 * @param int $property_id The property ID to check
 * @param int $user_id The user ID to verify against
 * @return bool True if user owns the property, false otherwise
 */
function verifyPropertyOwnership($property_id, $user_id) {
    global $con;
    
    $property_id = (int)$property_id;
    $user_id = (int)$user_id;
    
    $query = "SELECT uid FROM property WHERE pid = ? AND uid = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        error_log("Failed to prepare ownership verification query: " . mysqli_error($con));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $property_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    $owns_property = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    
    return $owns_property;
}

/**
 * Get property details with ownership verification
 * @param int $property_id The property ID
 * @param int $user_id The user ID to verify ownership
 * @return array|null Property data if owned by user, null otherwise
 */
function getPropertyForEdit($property_id, $user_id) {
    global $con;
    
    $property_id = (int)$property_id;
    $user_id = (int)$user_id;
    
    $query = "SELECT * FROM property WHERE pid = ? AND uid = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        error_log("Failed to prepare property retrieval query: " . mysqli_error($con));
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $property_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $property = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $property;
}

/**
 * Check if user has permission to edit properties
 * @param int $user_id The user ID
 * @return bool True if user can edit properties, false otherwise
 */
function canEditProperties($user_id) {
    global $con;
    
    $user_id = (int)$user_id;
    
    // Check if user is builder or agent
    $query = "SELECT utype FROM user WHERE uid = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        error_log("Failed to prepare user type check query: " . mysqli_error($con));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $user = mysqli_fetch_assoc($stmt);
    mysqli_stmt_close($stmt);
    
    if (!$user) {
        return false;
    }
    
    // Allow builders and agents to edit properties
    return in_array($user['utype'], ['builder', 'agent']);
}

/**
 * Sanitize property ID for security
 * @param mixed $property_id The property ID to sanitize
 * @return int Sanitized property ID
 */
function sanitizePropertyId($property_id) {
    return (int)$property_id;
}

/**
 * Generate secure edit URL
 * @param int $property_id The property ID
 * @return string Secure edit URL
 */
function generateEditUrl($property_id) {
    return "agent/edit-property.php?id=" . (int)$property_id;
}
?>
