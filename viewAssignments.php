<?php
require_once "config.php";

// Validate student_id
if (!isset($_GET["student_id"]) || empty(trim($_GET["student_id"]))) {
    echo "<p>Error: Missing or invalid student ID.</p>";
    exit();
}
$student_id = trim($_GET["student_id"]);

// Fetch student's name
$fname = "";
$lname = "";
$sql_name = "SELECT f_name, l_name FROM Project_Student WHERE student_id = ?";
if ($stmt = mysqli_prepare($link, $sql_name)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fname, $lname);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Sorting order
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'due_date';
$valid_sort_columns = ['due_date', 'class_name'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'due_date'; // Starts with due date
}

// Alphabetically sort by class name
$order_by_clause = $sort_by === 'class_name' ? 'class_name, assignment_id' : $sort_by;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Assignments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/viewAssignments.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style>
        .wrapper { width: 800px; margin: 0 auto; }
        .page-header h2 { margin-top: 0; }
        table tr td:last-child a { margin-right: 15px; }
        .sort-buttons { margin-bottom: 20px; }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header clearfix">
                    <h2 class="pull-left">Assignments</h2>
                    <a href="viewAssignments.php?student_id=<?php echo htmlspecialchars($student_id); ?>&sort_by=due_date" class="btn btn-default pull-right" style="margin-left: 10px;">Sort by Due Date</a>
                    <a href="viewAssignments.php?student_id=<?php echo htmlspecialchars($student_id); ?>&sort_by=class_name" class="btn btn-secondary pull-right">Sort by Class</a>
                </div>

                <h4>Assignments for <?php echo htmlspecialchars($fname . " " . $lname); ?> (SID: <?php echo htmlspecialchars($student_id); ?>)</h4><br>

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
    ORDER BY $order_by_clause
";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr>
                <th>Class</th>
                <th>Assignment ID</th>
                <th>Due Date</th>
                <th>Due Time</th>
                <th>Notes</th>
              </tr></thead><tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["class_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["assignment_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["due_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["due_time"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["notes"]) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='lead'><em>No assignments found for this student.</em></p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>Error: " . mysqli_error($link) . "</p>";
}
mysqli_close($link);
?>
<p><a href="index.php" class="btn btn-primary">Back</a></p>

</div>
</div>        
</div>
</body>
</html>
