<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SportOfficeDB";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("Database connection failed"));
    exit();
}

// Validate form data
$requiredFields = ['student_id', 'full_name', 'email', 'status', 'document'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field]) && empty($_FILES[$field])) {
        header("Location: ../view/signupView.php?status=error&message=" . urlencode("Missing required field: $field"));
        exit();
    }
}

$student_id = trim($_POST['student_id']);
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$status = trim($_POST['status']);
$page = isset($_POST['page']) ? trim($_POST['page']) : 'signup';

// Check for existing student ID or email
$checkStmt = $conn->prepare("SELECT student_id, email, approval_status FROM account_approvals WHERE student_id = ? OR email = ?");
if (!$checkStmt) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("Prepare failed: " . $conn->error));
    exit();
}
$checkStmt->bind_param("ss", $student_id, $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->bind_result($db_student_id, $db_email, $approval_status);
    $checkStmt->fetch();

    // Handle based on approval_status
    if ($approval_status === 'approved') {
        $message = ($db_student_id === $student_id) ?
            "This Student ID is already registered and approved." :
            "This email is already registered and approved.";
        $checkStmt->close();
        $conn->close();
        header("Location: ../view/signupView.php?status=error&message=" . urlencode($message));
        exit();
    } elseif ($approval_status === 'pending' || $approval_status === 'rejected') {
        // Update existing record for resubmission
        $checkStmt->close();

        // Validate file
        if (!isset($_FILES['document']) || $_FILES['document']['error'] == UPLOAD_ERR_NO_FILE) {
            header("Location: ../view/signupView.php?status=error&message=" . urlencode("No file uploaded"));
            exit();
        }

        $file = $_FILES['document'];
        $validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        if (!in_array($file['type'], $validTypes)) {
            header("Location: ../view/signupView.php?status=error&message=" . urlencode("Invalid file type. Use PDF, JPG, or PNG"));
            exit();
        }
        if ($file['size'] > $maxSize) {
            header("Location: ../view/signupView.php?status=error&message=" . urlencode("File size exceeds 5MB limit"));
            exit();
        }
        if ($file['error'] != UPLOAD_ERR_OK) {
            header("Location: ../view/signupView.php?status=error&message=" . urlencode("File upload error"));
            exit();
        }

        // Read file data
        $file_data = file_get_contents($file['tmp_name']);
        $file_name = $conn->real_escape_string($file['name']);
        $file_type = $file['type'];
        $file_size = $file['size'];

        // Update record
        $updateStmt = $conn->prepare("UPDATE account_approvals SET full_name = ?, email = ?, status = ?, file_name = ?, file_data = ?, file_type = ?, file_size = ?, approval_status = 'pending' WHERE student_id = ? OR email = ?");
        if (!$updateStmt) {
            header("Location: ../view/signupView.php?status=error&message=" . urlencode("Prepare failed: " . $conn->error));
            exit();
        }
        $updateStmt->bind_param("sssssssis", $full_name, $email, $status, $file_name, $file_data, $file_type, $file_size, $student_id, $email);

        if ($updateStmt->execute()) {
            $message = "Your signup request has been resubmitted. Awaiting admin approval.";
            $status = "success";
        } else {
            $message = "Error resubmitting request: " . $conn->error;
            $status = "error";
        }
        $updateStmt->close();
        $conn->close();
        header("Location: ../view/signupView.php?status=" . $status . "&message=" . urlencode($message));
        exit();
    }
}
$checkStmt->close();

// Proceed with new submission (no existing record)
if (!isset($_FILES['document']) || $_FILES['document']['error'] == UPLOAD_ERR_NO_FILE) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("No file uploaded"));
    exit();
}

$file = $_FILES['document'];
$validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
$maxSize = 5 * 1024 * 1024; // 5MB
if (!in_array($file['type'], $validTypes)) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("Invalid file type. Use PDF, JPG, or PNG"));
    exit();
}
if ($file['size'] > $maxSize) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("File size exceeds 5MB limit"));
    exit();
}
if ($file['error'] != UPLOAD_ERR_OK) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("File upload error"));
    exit();
}

// Read file data
$file_data = file_get_contents($file['tmp_name']);
$file_name = $conn->real_escape_string($file['name']);
$file_type = $file['type'];
$file_size = $file['size'];

// Insert into account_approvals
$stmt = $conn->prepare("INSERT INTO account_approvals (student_id, full_name, email, status, file_name, file_data, file_type, file_size, approval_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
if (!$stmt) {
    header("Location: ../view/signupView.php?status=error&message=" . urlencode("Prepare failed: " . $conn->error));
    exit();
}
$stmt->bind_param("sssssssi", $student_id, $full_name, $email, $status, $file_name, $file_data, $file_type, $file_size);

if ($stmt->execute()) {
    $message = "Signup request submitted. Awaiting admin approval.";
    $status = "success";
} else {
    $message = "Error submitting request: " . $conn->error;
    $status = "error";
}

$stmt->close();
$conn->close();

header("Location: ../view/signupView.php?status=" . $status . "&message=" . urlencode($message));
exit();
?>