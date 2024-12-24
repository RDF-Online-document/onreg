<?php
// Set header for JSON response
header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

// Get and decode JSON data
$json = file_get_contents('php://input');
$data = json_decode($json);

// Validate the ID
if (!isset($data->id) || !is_numeric($data->id)) {
    sendResponse(false, 'Invalid ID provided');
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create database connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Prepare delete statement
        $stmt = $conn->prepare("DELETE FROM Identification WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Bind parameters and execute
        $stmt->bind_param("i", $data->id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Check if any rows were affected
        if ($stmt->affected_rows === 0) {
            throw new Exception("No record found with the specified ID");
        }
        
        // Commit transaction
        $conn->commit();
        
        // Close statement
        $stmt->close();
        
        sendResponse(true, 'Record deleted successfully');
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        sendResponse(false, $e->getMessage());
    }
    
} catch (Exception $e) {
    sendResponse(false, "Database error: " . $e->getMessage());
} finally {
    // Close connection if it exists
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>