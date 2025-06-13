<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include('db.php');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'POST':
        addStudent();
        break;
    case 'GET':
        getStudents();
        break;
    case 'PUT':
        updateStudent();
        break;
    case 'DELETE':
        deleteStudent();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function addStudent() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    if (empty($input['name']) || empty($input['major']) || empty($input['email']) || empty($input['address'])) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required']);
        return;
    }
    
    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }
    
    // Check if email already exists
    $email_check = mysqli_real_escape_string($conn, $input['email']);
    $check_sql = "SELECT id FROM student WHERE email = '$email_check'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
        return;
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
}

function getStudents() {
    global $conn;
    
    $sql = "SELECT * FROM student ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
        return;
    }
    
    $students = [];
    while($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    
    echo json_encode($students);
}

function updateStudent() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Validate required fields
    if (empty($input['id']) || empty($input['name']) || empty($input['major']) || empty($input['email']) || empty($input['address'])) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields including ID are required']);
        return;
    }
    
    $student_id = (int)$input['id'];
    
    if ($student_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid student ID']);
        return;
    }
    
    // Validate email format
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }
    
    // Check if student exists
    $check_sql = "SELECT name FROM student WHERE id = $student_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (!$check_result || mysqli_num_rows($check_result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Student not found']);
        return;
    }
    
    // Check if email already exists for other students
    $email_check = mysqli_real_escape_string($conn, $input['email']);
    $email_check_sql = "SELECT id FROM student WHERE email = '$email_check' AND id != $student_id";
    $email_check_result = mysqli_query($conn, $email_check_sql);
    
    if (mysqli_num_rows($email_check_result) > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists for another student']);
        return;
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
}

function deleteStudent() {
    global $conn;
    
    // Check if ID is provided
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Student ID is required for deletion']);
        return;
    }
    
    $student_id = (int)$_GET['id'];
    
    if ($student_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid student ID']);
        return;
    }
    
    // First, check if student exists and get their name
    $check_sql = "SELECT name FROM student WHERE id = $student_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (!$check_result) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
        return;
    }
    
    if (mysqli_num_rows($check_result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Student not found']);
        return;
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
}

mysqli_close($conn);
?>