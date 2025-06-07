<?php
require_once "config.php";

// Get class_id (required)
if (!isset($_GET["class_id"]) || empty(trim($_GET["class_id"]))) {
    echo "<p>Error: Missing or invalid class ID.</p>";
    exit();
}
$class_id = trim($_GET["class_id"]);

// for redirect back
$instructor_id = isset($_GET["instructor_id"]) ? trim($_GET["instructor_id"]) : null;

// Initialize variables
$notes = $due_date = $due_time = "";
$notes_err = $due_date_err = $due_time_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notes = trim($_POST["notes"]);
    $due_date = trim($_POST["due_date"]);
    $due_time = trim($_POST["due_time"]);

    // Basic validation
    if (empty($notes)) $notes_err = "Please enter a class description (or name).";
    if (empty($due_date)) $due_date_err = "Please enter a due date.";
    if (empty($due_time)) $due_time_err = "Please enter a due time.";

    if (empty($name_err) && empty($due_date_err) && empty($due_time_err)) {
        $sql = "INSERT INTO Project_Assignment (class_id, notes, due_date, due_time)
                VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isss", $class_id, $notes, $due_date, $due_time);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: viewAssignmentsInstructor.php?class_id=$class_id" . ($instructor_id ? "&instructor_id=$instructor_id" : ""));
                exit();
            } else {
                echo "<p>Error inserting assignment: " . mysqli_error($link) . "</p>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Assignment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>.wrapper { width: 600px; margin: 0 auto; }</style>
</head>
<body>
<div class="wrapper">
    <h2>Add Assignment</h2>
    <p>Fill out the form to create a new assignment for Class ID <?php echo htmlspecialchars($class_id); ?>.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?class_id=$class_id" . ($instructor_id ? "&instructor_id=$instructor_id" : ""); ?>" method="post">
        <div class="form-group">
            <label>Assignment Description</label>
            <textarea name="notes" class="form-control"><?php echo htmlspecialchars($notes); ?></textarea>
        </div>
        <div class="form-group <?php echo (!empty($due_date_err)) ? 'has-error' : ''; ?>">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($due_date); ?>">
            <span class="help-block"><?php echo $due_date_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($due_time_err)) ? 'has-error' : ''; ?>">
            <label>Due Time</label>
            <input type="time" name="due_time" class="form-control" value="<?php echo htmlspecialchars($due_time); ?>">
            <span class="help-block"><?php echo $due_time_err; ?></span>
        </div>
        <input type="submit" class="btn btn-primary" value="Add Assignment">
        <a href="viewAssignmentsInstructor.php?class_id=<?php echo $class_id . ($instructor_id ? "&instructor_id=$instructor_id" : ""); ?>" class="btn btn-default">Cancel</a>
    </form>
</div>
</body>
</html>
