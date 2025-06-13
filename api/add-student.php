<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
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
if (empty($input['name']) || empty($input['major']) || empty($input['email']) || empty($input['address'])) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit();
}

// Validate email format
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

// Check if email already exists
$email_check = mysqli_real_escape_string($conn, $input['email']);
$check_sql = "SELECT id FROM student WHERE email = '$email_check'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Email already exists']);
    exit();
}

$name = mysqli_real_escape_string($conn, trim($input['name']));
$major = mysqli_real_escape_string($conn, trim($input['major']));
$email = mysqli_real_escape_string($conn, trim($input['email']));
$address = mysqli_real_escape_string($conn, trim($input['address']));

$sql = "INSERT INTO student (name, major, email, address) 
        VALUES ('$name', '$major', '$email', '$address')";

if(mysqli_query($conn, $sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Student added successfully!',
        'id' => mysqli_insert_id($conn)
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
