<!-- 
Group: 20
Members: Xavier Ashkar, Joy Lim, Kevin Tran 
-->

<?php
require_once "config.php";

// Validate class_id
if (!isset($_GET["class_id"]) || empty(trim($_GET["class_id"]))) {
    echo "<p>Error: Missing or invalid class ID.</p>";
    exit();
}
$class_id = trim($_GET["class_id"]);
$instructor_id = isset($_GET["instructor_id"]) ? trim($_GET["instructor_id"]) : null;

// In case we dont get instructor_id
if (!$instructor_id) {
    $sql_get_instructor = "SELECT instructor_id FROM Project_Class WHERE class_id = ?";
    if ($stmt = mysqli_prepare($link, $sql_get_instructor)) {
        mysqli_stmt_bind_param($stmt, "i", $class_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $instructor_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Get class name
$class_name = "";
$sql_name = "SELECT class_name FROM Project_Class WHERE class_id = ?";
if ($stmt = mysqli_prepare($link, $sql_name)) {
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $class_name);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Assignments (Instructor)</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/viewInstructorAssignments.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style>
        .wrapper { width: 800px; margin: 0 auto; }
        .page-header h2 { margin-top: 0; }
        table tr td:last-child a { margin-right: 15px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header clearfix">
                    <h2 class="pull-left">Assignments for Class</h2>
                    <a href="addAssignment.php?class_id=<?php echo $class_id; ?>&instructor_id=<?php echo $instructor_id; ?>&from=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-success">Add Assignment</a>
                </div>

                <h4>Class: <?php echo htmlspecialchars($class_name); ?> (Class ID: <?php echo htmlspecialchars($class_id); ?>)</h4><br>

<?php
$sql = "
    SELECT 
        assignment_id,
        due_date,
        due_time,
        notes
    FROM Project_Assignment
    WHERE class_id = ?
    ORDER BY due_date, due_time
";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr>
                <th>Assignment ID</th>
                <th>Due Date</th>
                <th>Due Time</th>
                <th>Assignment Details</th>
                <th>Action</th>
              </tr></thead><tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["assignment_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["due_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["due_time"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["notes"]) . "</td>";
            echo "<td>
                    <a href='deleteAssignment.php?assignment_id=" . $row["assignment_id"] . "&class_id=$class_id" . ($instructor_id ? "&instructor_id=$instructor_id" : "") . "' 
                    title='Delete' data-toggle='tooltip' class='delete-icon' onclick=\"return confirm('Are you sure you want to delete this assignment?');\">
                        <span class='glyphicon glyphicon-remove'></span>
                    </a>
                    <a href='editAssignment.php?assignment_id={$row['assignment_id']}&class_id=$class_id&instructor_id=$instructor_id' title='Edit' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>
                </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='lead'><em>No assignments found for this class.</em></p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>Error: " . mysqli_error($link) . "</p>";
}
mysqli_close($link);
?>
<p><a href="viewInstructorSchedule.php?instructor_id=<?php echo htmlspecialchars($instructor_id); ?>" class="btn btn-primary">Back</a></p>

</div>
</div>        
</div>
</body>
</html>
