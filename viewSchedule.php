<?php
session_start();
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Class Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style>
        .wrapper {
            width: 800px;
            margin: 0 auto;
        }
        .page-header h2 {
            margin-top: 0;
        }
        table tr td:last-child a {
            margin-right: 15px;
        }
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
                    <h2 class="pull-left">View Class Schedule</h2>
                    <!-- Add Class button at top, WORK ON LATER -->
                    <a href="addClass.php" class="btn btn-success pull-right">Add Class</a>
                </div>

<?php
if (isset($_GET["SID"]) && !empty(trim($_GET["SID"]))) {
    $_SESSION["SID"] = $_GET["SID"];
}
if (isset($_GET["l_name"]) && !empty(trim($_GET["l_name"]))) {
    $_SESSION["l_name"] = $_GET["l_name"];
}

if (isset($_SESSION["SID"])) {
    $sql = "SELECT 
                c.class_id AS ClassID,
                c.className AS ClassName,
                c.date AS ClassDate,
                c.time AS ClassTime,
                c.location AS Location,
                c.instructor_id AS InstructorID
            FROM Project_Class AS c
            JOIN Project_Attends AS a ON c.class_id = a.class_id
            JOIN Project_Student AS s ON a.student_id = s.student_id
            WHERE s.student_id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_SID);
        $param_SID = $_SESSION["SID"];
        $Lname = $_SESSION["l_name"];

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            echo "<h4>Class Schedule for $Lname (SID: $param_SID)</h4><br>";

            if (mysqli_num_rows($result) > 0) {
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead><tr>";
                echo "<th>Class ID</th><th>Class Name</th><th>Date</th><th>Time</th><th>Location</th><th>Instructor ID</th>";
                echo "</tr></thead><tbody>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['ClassID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ClassName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ClassDate']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ClassTime']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['InstructorID']) . "</td>";
                    echo "</tr>";
                }

                echo "</tbody></table>";
                mysqli_free_result($result);
            } else {
                echo "<p class='lead'><em>No classes found for this student.</em></p>";
            }
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
} else {
    header("location: error.php");
    exit();
}
?>
<p><a href="index.php" class="btn btn-primary">Back</a></p>
</div>
</div>        
</div>
</body>
</html>
