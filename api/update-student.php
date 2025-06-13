<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow PUT method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use PUT.']);
    exit();
}

include('db.php');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
if (empty($input['id']) || empty($input['name']) || empty($input['major']) || empty($input['email']) || empty($input['address'])) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields including ID are required']);
    exit();
}

$student_id = (int)$input['id'];

if ($student_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid student ID']);
    exit();
}

// Validate email format
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

// Check if student exists
$check_sql = "SELECT name FROM student WHERE id = $student_id";
$check_result = mysqli_query($conn, $check_sql);

if (!$check_result || mysqli_num_rows($check_result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Student not found']);
    exit();
}

// Check if email already exists for other students
$email_check = mysqli_real_escape_string($conn, $input['email']);
$email_check_sql = "SELECT id FROM student WHERE email = '$email_check' AND id != $student_id";
$email_check_result = mysqli_query($conn, $email_check_sql);

if (mysqli_num_rows($email_check_result) > 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Email already exists for another student']);
    exit();
}

$name = mysqli_real_escape_string($conn, trim($input['name']));
$major = mysqli_real_escape_string($conn, trim($input['major']));
$email = mysqli_real_escape_string($conn, trim($input['email']));
$address = mysqli_real_escape_string($conn, trim($input['address']));

$update_sql = "UPDATE student SET 
               name = '$name', 
               major = '$major', 
               email = '$email', 
               address = '$address' 
               WHERE id = $student_id";

if (mysqli_query($conn, $update_sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Student '$name' updated successfully"
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'No changes were made'
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>