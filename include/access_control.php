<?php
// Access control functions for role-based permissions

function checkUserRole($requiredRoles = []) {
    if (!isset($_SESSION['uid'])) {
        return false;
    }
    
    global $con;
    $uid = $_SESSION['uid'];
    
    $query = "SELECT utype FROM user WHERE uid = '$uid'";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $userRole = $row['utype'];
        return in_array($userRole, $requiredRoles);
    }
    
    return false;
}

function isBuilder() {
    return checkUserRole(['builder']);
}

function isAgent() {
    return checkUserRole(['agent']);
}

function isBuilderOrAgent() {
    return checkUserRole(['builder', 'agent']);
}

function isRegularUser() {
    return checkUserRole(['user']);
}

function getUserRole() {
    if (!isset($_SESSION['uid'])) {
        return 'guest';
    }
    
    global $con;
    $uid = $_SESSION['uid'];
    
    $query = "SELECT utype FROM user WHERE uid = '$uid'";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['utype'];
    }
    
    return 'guest';
}

function redirectToLogin() {
    header("Location: login.php");
    exit();
}

function redirectToHome() {
    header("Location: index.php");
    exit();
}

function checkPropertyAccess($propertyId) {
    global $con;
    
    // Check if property exists and is available
    $query = "SELECT purchase_status, uid FROM property WHERE pid = '$propertyId'";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return [
            'exists' => true,
            'isAvailable' => $row['purchase_status'] === 'available',
            'ownerId' => $row['uid']
        ];
    }
    
    return ['exists' => false];
}

function canEditProperty($propertyId) {
    if (!isset($_SESSION['uid'])) {
        return false;
    }
    
    $access = checkPropertyAccess($propertyId);
    if (!$access['exists']) {
        return false;
    }
    
    $currentUserId = $_SESSION['uid'];
    $userRole = getUserRole();
    
    // Builders and agents can edit their own properties
    if (isBuilderOrAgent() && $access['ownerId'] == $currentUserId) {
        return true;
    }
    
    return false;
}
?>
