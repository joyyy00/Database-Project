<?php
session_start();
require_once "config.php";

// Check for student_id in the GET request
if (isset($_GET["student_id"]) && !empty(trim($_GET["student_id"]))) {
    $SID = trim($_GET["student_id"]);
    $_SESSION["SID"] = $SID;
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    // If no valid student_id in GET and not a POST request, redirect
    header("location: error.php");
    exit();
}

// Delete the student's record after confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["SID"]) && !empty($_SESSION["SID"])) {
        $SID = $_SESSION["SID"];

        // Prepare a delete statement
        $sql = "DELETE FROM Project_Student WHERE student_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_SID);
            $param_SID = $SID;

            if (mysqli_stmt_execute($stmt)) {
                // Successfully deleted
                header("location: index.php");
                exit();
            } else {
                echo "Error deleting the student.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Student Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>.wrapper { width: 500px; margin: 0 auto; }</style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Student Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
							<input type="hidden" name="SID" value="<?php echo htmlspecialchars($SID); ?>"/>
							<p>Are you sure you want to delete the record for Student ID: <strong><?php echo htmlspecialchars($SID); ?></strong>?</p><br>
                            <input type="submit" value="Yes" class="btn btn-danger">
                            <a href="index.php" class="btn btn-default">No</a>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
