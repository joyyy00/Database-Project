<?php
require_once "config.php";

// Validate instructor_id
if (!isset($_GET["instructor_id"]) || empty(trim($_GET["instructor_id"]))) {
    echo "<p>Error: Missing or invalid instructor ID.</p>";
    exit();
}
$instructor_id = trim($_GET["instructor_id"]);

// Fetch instructor name
$fname = "";
$lname = "";
$sql_name = "SELECT f_name, l_name FROM Project_Instructor WHERE instructor_id = ?";
if ($stmt = mysqli_prepare($link, $sql_name)) {
    mysqli_stmt_bind_param($stmt, "i", $instructor_id);
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
    <title>Instructor Classes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/viewInstructorSchedule.css">
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
                    <h2 class="pull-left">Classes Taught</h2>
                    <a href="addInstructorClass.php?instructor_id=<?php echo htmlspecialchars($instructor_id); ?>" class="btn btn-success pull-right">Add Class</a>
                    <!-- Currently drop class is unavailable because of foreign key constraints -->
                    <!-- <a href="dropInstructorClass.php?instructor_id=<?php echo htmlspecialchars($instructor_id); ?>" class="btn btn-drop pull-right">Drop Class</a> -->
                </div>

                <h4>Class Schedule for <?php echo htmlspecialchars($fname . " " . $lname); ?> (ID: <?php echo htmlspecialchars($instructor_id); ?>)</h4><br>

<?php
$sql = "
    SELECT 
        c.class_id,
        c.class_name,
        c.start_date,
        c.end_date,
        c.time,
        c.location,
        GROUP_CONCAT(d.day_of_week ORDER BY 
            FIELD(d.day_of_week, 'M', 'T', 'W', 'R', 'F') SEPARATOR '/') AS days
    FROM Project_Class c
    LEFT JOIN Project_Class_Days d ON c.class_id = d.class_id
    WHERE c.instructor_id = ?
    GROUP BY c.class_id
";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $instructor_id);
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
                <th>Actions</th>
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
            echo "<td>
                    <a href='viewInstructorAssignments.php?class_id=" . $row["class_id"] . "&instructor_id=" . $instructor_id . "&from=viewInstructorSchedule.php?instructor_id=" . $instructor_id . "' title='View/Add Assignments' data-toggle='tooltip'>
                        <span class='glyphicon glyphicon-book'></span>
                    </a>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='lead'><em>No classes assigned to this instructor.</em></p>";
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
