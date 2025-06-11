<!-- 
Group: 20
Members: Xavier Ashkar, Joy Lim, Kevin Tran 
-->

<?php
require_once "config.php";

$instructor_id = $_GET['instructor_id'] ?? null;

if (!$instructor_id) {
    die("Invalid request. Instructor ID is required.");
}

// Fetch instructor name
$instructor_name = "Unknown Instructor";
$sql_name = "SELECT CONCAT(f_name, ' ', l_name) AS full_name FROM Project_Instructor WHERE instructor_id = ?";
if ($stmt_name = mysqli_prepare($link, $sql_name)) {
    mysqli_stmt_bind_param($stmt_name, "i", $instructor_id);
    if (mysqli_stmt_execute($stmt_name)) {
        $result_name = mysqli_stmt_get_result($stmt_name);
        if ($row = mysqli_fetch_assoc($result_name)) {
            $instructor_name = $row['full_name'];
        }
    }
    mysqli_stmt_close($stmt_name);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    $sql = "UPDATE Project_Class SET instructor_id = NULL WHERE class_id = ? AND instructor_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $class_id, $instructor_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: dropInstructorClasses.php?instructor_id=$instructor_id&message=Class dropped successfully");
            exit();
        } else {
            echo "Oops! Something went wrong.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch instructor's classes
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
$classes = [];

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $instructor_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $classes[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Drop Instructor Classes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/dropClasses.css">
    <style>
        .wrapper { width: 800px; margin: 0 auto; }
        .page-header h2 { margin-top: 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="page-header clearfix">
            <h2 class="pull-left">Drop Instructor Classes</h2>
            <a href="viewInstructorSchedule.php?instructor_id=<?php echo htmlspecialchars($instructor_id); ?>" class="btn btn-primary pull-right">Back</a>
        </div>

        <h4>Class Schedule for <?php echo htmlspecialchars($instructor_name); ?> (ID: <?php echo htmlspecialchars($instructor_id); ?>)</h4><br>

        <?php if (isset($_GET['message'])): ?>
            <div class='alert alert-success'><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>

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
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?instructor_id=" . htmlspecialchars($instructor_id); ?>" method="post" style="display:inline;">
                                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class['class_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to drop this class?');">Drop</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="lead"><em>No classes found for this instructor.</em></p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
