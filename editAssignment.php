<!-- 
Group: 20
Members: Xavier Ashkar, Joy Lim, Kevin Tran 
-->

<?php
require_once "config.php";

$instructor_id = $_GET['instructor_id'] ?? null;
$assignment_id = $_GET['assignment_id'] ?? null;

if (!$assignment_id) {
    die("Invalid request. Assignment ID is required.");
}

$assignment = [];
$class_id = null;
$success_msg = $error_msg = "";

// Fetch assignment details
$sql_fetch = "SELECT * FROM Project_Assignment WHERE assignment_id = ?";
if ($stmt = mysqli_prepare($link, $sql_fetch)) {
    mysqli_stmt_bind_param($stmt, "i", $assignment_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $assignment = $row;
            $class_id = $row['class_id'];
        } else {
            die("Assignment not found.");
        }
        mysqli_free_result($result);
    } else {
        die("Query failed.");
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];
    $notes = $_POST['notes'];

    $sql_update = "UPDATE Project_Assignment SET due_date = ?, due_time = ?, notes = ? WHERE assignment_id = ?";
    if ($stmt = mysqli_prepare($link, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "sssi", $due_date, $due_time, $notes, $assignment_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: viewInstructorAssignments.php?class_id=" . $class_id . "&instructor_id=" . $instructor_id);
            exit();
        } else {
            $error_msg = "Error updating assignment.";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/editAssignment.css">
</head>
<body>
<div class="wrapper">
    <h2>Edit Assignment</h2>
    <br><br>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($assignment['due_date']); ?>" required>
        </div>
        <div class="form-group">
            <label>Due Time</label>
            <input type="time" name="due_time" class="form-control" value="<?php echo htmlspecialchars($assignment['due_time']); ?>" required>
        </div>
        <div class="form-group">
            <label>Assignment Details</label>
            <textarea name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($assignment['notes']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-blue">Update Assignment</button>
        <a href="viewInstructorAssignments.php?class_id=<?php echo htmlspecialchars($class_id); ?>&instructor_id=<?php echo htmlspecialchars($instructor_id); ?>" class="btn btn-default">Cancel</a>
    </form>
</div>
</body>
</html>
