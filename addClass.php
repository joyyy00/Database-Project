<!-- 
Group: 20
Members: Xavier Ashkar, Joy Lim, Kevin Tran 
-->

<?php
// Include config file
require_once "config.php";

// Get the student_id from the query string
$student_id = $_GET['student_id'] ?? null;
if (!$student_id) die("Invalid request. Student ID is required.");

// Fetch the student's name
$student_name = "Unknown Student";
$sql_name = "SELECT CONCAT(f_name, ' ', l_name) AS full_name FROM Project_Student WHERE student_id = ?";
if ($stmt_name = mysqli_prepare($link, $sql_name)) {
    mysqli_stmt_bind_param($stmt_name, "i", $param_student_id);
    $param_student_id = $student_id;
    if (mysqli_stmt_execute($stmt_name)) {
        $result_name = mysqli_stmt_get_result($stmt_name);
        if ($row_name = mysqli_fetch_assoc($result_name)) {
            $student_name = $row_name['full_name'];
        }
        mysqli_free_result($result_name);
    }
    mysqli_stmt_close($stmt_name);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $param_student_id = $student_id;
    $param_class_id = $class_id;

    $sql = "INSERT INTO Project_Attends (student_id, class_id) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $param_student_id, $param_class_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: addClass.php?student_id=$student_id&message=Class added successfully");
            exit();
        } else {
            $error = mysqli_error($link);
            if (str_contains($error, "Class is full")) {
                header("Location: addClass.php?student_id=$student_id&error=classfull");
            } elseif (str_contains($error, "Student has reached max classes")) {
                header("Location: addClass.php?student_id=$student_id&error=maxclasses");
            } else {
                header("Location: addClass.php?student_id=$student_id&error=unknown");
            }
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch classes the student is not enrolled in
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
    LEFT JOIN Project_Attends a ON c.class_id = a.class_id AND a.student_id = ?
    LEFT JOIN Project_Class_Days d ON c.class_id = d.class_id
    WHERE a.class_id IS NULL
    GROUP BY c.class_id
";
$classes = [];
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_student_id);
    $param_student_id = $student_id;
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $classes[] = $row;
        }
        mysqli_free_result($result);
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Classes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/addClass.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style>
        .wrapper { width: 800px; margin: 0 auto; }
        .page-header h2 { margin-top: 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header clearfix">
                    <h2 class="pull-left">Add Classes</h2>
                    <a href="viewSchedule.php?student_id=<?php echo htmlspecialchars($student_id); ?>" class="btn btn-primary pull-right">Back</a>
                </div>

                <h4>Available Classes for <?php echo htmlspecialchars($student_name); ?> (SID: <?php echo htmlspecialchars($student_id); ?>)</h4><br>

                <?php
                if (isset($_GET['message'])) {
                    echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['message']) . "</div>";
                } elseif (isset($_GET['error'])) {
                    if ($_GET['error'] === 'classfull') {
                        echo "<div class='alert alert-danger'>This class is full (10). Please choose another.</div>";
                    } elseif ($_GET['error'] === 'maxclasses') {
                        echo "<div class='alert alert-danger'>You have reached the maximum number of classes (5).</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Oops! Something went wrong. Please try again.</div>";
                    }
                }
                ?>

                <?php if (!empty($classes)): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Class ID</th>
                                <th>Class Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Instructor ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($class['end_date']); ?></td>
                                    <td><?php echo htmlspecialchars($class['days']); ?></td>
                                    <td><?php echo htmlspecialchars($class['time']); ?></td>
                                    <td><?php echo htmlspecialchars($class['location']); ?></td>
                                    <td><?php echo htmlspecialchars($class['instructor_id']); ?></td>
                                    <td>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?student_id=" . htmlspecialchars($student_id); ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Add</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="lead"><em>No available classes to add for this student.</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
