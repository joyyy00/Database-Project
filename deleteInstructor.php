<?php
require_once "config.php";

// Step 1: Validate GET or POST request
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_GET["instructor_id"]) || empty(trim($_GET["instructor_id"]))) {
        header("location: error.php");
        exit();
    }
    $ID = trim($_GET["instructor_id"]);
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["instructor_id"]) && !empty($_POST["instructor_id"])) {
        $ID = $_POST["instructor_id"];

        $sql = "DELETE FROM Project_Instructor WHERE instructor_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_id);
            $param_id = $ID;

            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Error: Could not delete instructor.";
            }

            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    } else {
        echo "Invalid request.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Instructor Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>.wrapper { width: 500px; margin: 0 auto; }</style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Instructor Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="instructor_id" value="<?php echo htmlspecialchars($ID); ?>"/>
                            <p>Are you sure you want to delete the record for Instructor ID: <strong><?php echo htmlspecialchars($ID); ?></strong>?</p><br>
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
