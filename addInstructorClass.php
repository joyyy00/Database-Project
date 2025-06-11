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

// Get instructor name
$instructor_name = "Unknown";
$sql_name = "SELECT CONCAT(f_name, ' ', l_name) AS full_name FROM Project_Instructor WHERE instructor_id = ?";
if ($stmt = mysqli_prepare($link, $sql_name)) {
    mysqli_stmt_bind_param($stmt, "i", $instructor_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $instructor_name);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $sql_update = "UPDATE Project_Class SET instructor_id = ? WHERE class_id = ?";
    if ($stmt = mysqli_prepare($link, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "ii", $instructor_id, $class_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: viewInstructorSchedule.php?instructor_id=$instructor_id&message=Class+assigned");
            exit();
        } else {
            echo "Error assigning class.";
        }
        mysqli_stmt_close($stmt);
    }
}

// fetch classes not assigned to this instructor
$sql = "
    SELECT 
        c.class_id, c.class_name, c.start_date, c.end_date, c.time, c.location,
        GROUP_CONCAT(d.day_of_week ORDER BY FIELD(d.day_of_week, 'M', 'T', 'W', 'R', 'F') SEPARATOR '/') AS days
    FROM Project_Class c
    LEFT JOIN Project_Class_Days d ON c.class_id = d.class_id
    WHERE (c.instructor_id IS NULL OR c.instructor_id != ?)
    GROUP BY c.class_id
";
$classes = [];
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $instructor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $classes[] = $row;
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Class to Instructor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/addClass.css">
    <style>
        .wrapper { width: 800px; margin: 0 auto; }
        .page-header h2 { margin-top: 0; }
        table tr td:last-child a { margin-right: 15px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="page-header clearfix">
        <h2 class="pull-left">Assign Class</h2>
        <a href="viewInstructorSchedule.php?instructor_id=<?php echo htmlspecialchars($instructor_id); ?>" class="btn btn-primary pull-right">Back</a>
    </div>
    <h4>Available Classes for <?php echo htmlspecialchars($instructor_name); ?> (ID: <?php echo htmlspecialchars($instructor_id); ?>)</h4>
    <br>

    <?php if (!empty($classes)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Class ID</th>
                    <th>Name</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Days</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?= htmlspecialchars($class['class_id']) ?></td>
                        <td><?= htmlspecialchars($class['class_name']) ?></td>
                        <td><?= htmlspecialchars($class['start_date']) ?></td>
                        <td><?= htmlspecialchars($class['end_date']) ?></td>
                        <td><?= htmlspecialchars($class['days']) ?></td>
                        <td><?= htmlspecialchars($class['time']) ?></td>
                        <td><?= htmlspecialchars($class['location']) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="class_id" value="<?= htmlspecialchars($class['class_id']) ?>">
                                <button type="submit" class="btn btn-success btn-sm">Assign</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="lead"><em>No available classes to assign.</em></p>
    <?php endif; ?>
</div>
</body>
</html>
