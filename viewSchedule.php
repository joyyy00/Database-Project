<!-- 
Group: 20
Members: Xavier Ashkar, Joy Lim, Kevin Tran 
-->

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/viewSchedule.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style>
        .wrapper { width: 800px; margin: 0 auto; }
        .page-header h2 { margin-top: 0; }
        table tr td:last-child a { margin-right: 15px; }
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
                    <h2 class="pull-left">Class Schedule</h2>
                    <a href="addClass.php?student_id=<?php echo htmlspecialchars($student_id); ?>" class="btn btn-add pull-right">Add Class</a>
                    <a href="dropClasses.php?student_id=<?php echo htmlspecialchars($student_id); ?>" class="btn btn-drop pull-right">Drop Class</a>
                </div>

                <h4>Class Schedule for <?php echo htmlspecialchars($fname . " " .$lname); ?> (SID: <?php echo htmlspecialchars($student_id); ?>)</h4><br>

<?php
$sql = "
    SELECT 
        c.class_id,
        c.class_name,
        c.start_date,
        c.end_date,
        c.time,
        c.location,
        c.instructor_id,
        GROUP_CONCAT(d.day_of_week ORDER BY 
            FIELD(d.day_of_week, 'M', 'T', 'W', 'R', 'F') SEPARATOR '/') AS days
    FROM Project_Class c
    JOIN Project_Attends a ON c.class_id = a.class_id
    LEFT JOIN Project_Class_Days d ON c.class_id = d.class_id
    WHERE a.student_id = ?
    GROUP BY c.class_id
";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days</th>
                <th>Time</th>
                <th>Location</th>
                <th>Instructor ID</th>
              </tr></thead><tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["class_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["class_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["start_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["end_date"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["days"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["time"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["location"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["instructor_id"]) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='lead'><em>No class schedule found for this student.</em></p>";
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
