<!-- 
TODO
- Change format of choosing class statistic options
- Create individual files for options 
  Currently options are coded in this files
-->

<?php
require_once "config.php";

// Fetch all class names for the dropdown
$classOptions = [];
$sql = "SELECT class_id, class_name FROM Project_Class";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $classOptions[] = $row;
    }
}

// Handle form submission
$resultText = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = $_POST["class_id"] ?? "";
    $stat_type = $_POST["stat_type"] ?? "";

    if (!empty($class_id) && !empty($stat_type)) {
        if ($stat_type === "list_students") {
            $sql = "SELECT s.f_name, s.l_name
                    FROM Project_Student AS s
                    JOIN Project_Attends AS a ON s.student_id = a.student_id
                    WHERE a.class_id = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $class_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $resultText .= "<h4>Students in selected class:</h4><ul>";
                while ($row = mysqli_fetch_assoc($res)) {
                    $resultText .= "<li>" . htmlspecialchars($row['f_name']) . " " . htmlspecialchars($row['l_name']) . "</li>";
                }
                $resultText .= "</ul>";
                mysqli_stmt_close($stmt);
            }
        } elseif ($stat_type === "count_students") {
            $sql = "SELECT COUNT(a.student_id) AS total
                    FROM Project_Attends AS a
                    WHERE a.class_id = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $class_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $total);
                mysqli_stmt_fetch($stmt);
                $resultText .= "<h4>Total Students:</h4><p><strong>$total</strong></p>";
                mysqli_stmt_close($stmt);
            }
        }
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Statistics</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <!-- Link to style -->
    <link rel="stylesheet" href="css/classStats.css">
</head>
<body>
<div class="wrapper">
    <h2>Class Statistics & Logistics</h2>
    <form method="post" action="classStats.php">
        <div class="form-group">
            <label for="class_id">Select Class</label>
            <select name="class_id" id="class_id" class="form-control" required>
                <option value="">--Choose Class--</option>
                <?php foreach ($classOptions as $class): ?>
                    <option value="<?= htmlspecialchars($class['class_id']) ?>">
                        <?= htmlspecialchars($class['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="stat_type">Statistic Type</label>
            <select name="stat_type" id="stat_type" class="form-control" required>
                <option value="">--Choose Type--</option>
                <option value="list_students">List Students</option>
                <option value="count_students">Count Students</option>
            </select>
        </div>

        <input type="submit" class="btn btn-primary" value="View Statistics">
        <a href="index.php" class="btn btn-default">Back to Home</a>
    </form>

    <hr>
    <?= $resultText ?>
</div>
</body>
</html>
