<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow DELETE method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use DELETE.']);
    exit();
}

include('db.php');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Student ID is required for deletion']);
    exit();
}

$student_id = (int)$_GET['id'];

if ($student_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid student ID']);
    exit();
}

// First, check if student exists and get their name
$check_sql = "SELECT name FROM student WHERE id = $student_id";
$check_result = mysqli_query($conn, $check_sql);

if (!$check_result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit();
}

if (mysqli_num_rows($check_result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Student not found']);
    exit();
}

$student = mysqli_fetch_assoc($check_result);
$student_name = $student['name'];

// Delete the student
$delete_sql = "DELETE FROM student WHERE id = $student_id";

if (mysqli_query($conn, $delete_sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Student '$student_name' deleted successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete student']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>