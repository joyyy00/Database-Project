<?php
session_start();
require_once "config.php";

// Validate student_id from URL
if (!isset($_GET["student_id"]) || empty(trim($_GET["student_id"]))) {
    echo "<p>Error: Missing or invalid student ID.</p>";    exit();
}

$student_id = trim($_GET["student_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Assignments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<body>
<div class="container">
    <h2>Assignments for Student ID: <?php echo htmlspecialchars($student_id); ?></h2>
<?php
$sql = "
    SELECT 
        c.class_name,
        a.assignment_id,
        a.due_date,
        a.due_time,
        a.notes
    FROM Project_Assignment a
    JOIN Project_Class c ON a.class_id = c.class_id
    JOIN Project_Attends att ON c.class_id = att.class_id
    WHERE att.student_id = ?
";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<tr><th>Class</th><th>Assignment ID</th><th>Due Date</th><th>Due Time</th><th>Notes</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["class_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["assignment_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["due_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["due_time"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["notes"]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No assignments found for this student.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>SQL Error: " . mysqli_error($link) . "</p>";
}

mysqli_close($link);
?>
    <a href="index.php" class="btn btn-primary">Back</a>
</div>
</body>
</html>
