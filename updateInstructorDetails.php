<!-- 
Group: 20
Members: Xavier Ashkar, Joy Lim, Kevin Tran 
-->

<?php
    session_start();
    // Include config file
    require_once "config.php";

    // Define variables and initialize with empty values
    $f_name = $l_name = $email = "";
    $f_name_err = $l_name_err = $email_err = $update_err = "";

    if (isset($_GET["instructor_id"]) && !empty(trim($_GET["instructor_id"]))) {
        $_SESSION["instructor_id"] = $_GET["instructor_id"];

        // Prepare a select statement
        $sql1 = "SELECT * FROM Project_Instructor WHERE instructor_id = ?";

        if ($stmt1 = mysqli_prepare($link, $sql1)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt1, "s", $param_ID);
            // Set parameters
            $param_ID = trim($_GET["instructor_id"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt1)) {
                $result1 = mysqli_stmt_get_result($stmt1);
                if (mysqli_num_rows($result1) > 0) {
                    $row = mysqli_fetch_array($result1);

                    $f_name = $row['f_name'];
                    $l_name = $row['l_name'];
                    $email = $row['email'];
                }
            }
        }
    }

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $instructor_id = $_SESSION["instructor_id"];

        // Validate first name
        $new_f_name = trim($_POST["f_name"]);
        if (empty($new_f_name)) {
            $new_f_name = $f_name; // Retain old value if empty
        } elseif (!filter_var($new_f_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
            $f_name_err = "Please enter a valid first name.";
        }

        // Validate last name
        $new_l_name = trim($_POST["l_name"]);
        if (empty($new_l_name)) {
            $new_l_name = $l_name; // Retain old value if empty
        } elseif (!filter_var($new_l_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
            $l_name_err = "Please enter a valid last name.";
        }

        // Validate email
        $new_email = trim($_POST["email"]);
        if (empty($new_email)) {
            $new_email = $email; // Retain old value if empty
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        }

        // Check if all values are the same
        if ($new_f_name === $f_name && $new_l_name === $l_name && $new_email === $email) {
            $update_err = "No changes were made. Please modify at least one field.";
        }

        // Check input errors before updating the database
        if (empty($f_name_err) && empty($l_name_err) && empty($email_err) && empty($update_err)) {
            $sql = "UPDATE Project_Instructor SET f_name = ?, l_name = ?, email = ? WHERE instructor_id = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssss", $param_f_name, $param_l_name, $param_email, $param_ID);

                $param_f_name = $new_f_name;
                $param_l_name = $new_l_name;
                $param_email = $new_email;
                $param_ID = $instructor_id;

                if (mysqli_stmt_execute($stmt)) {
                    header("location: index.php");
                    exit();
                } else {
                    echo "Error updating record.";
                }
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Instructor Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/updateStudentInstructorDetails.css">
    <style type="text/css">
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h3>Update Record for Instructor ID: <?php echo htmlspecialchars($_SESSION["instructor_id"]); ?></h3>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <?php 
                    if (!empty($update_err)) {
                        echo '<div class="alert alert-danger">' . $update_err . '</div>';
                    }
                    ?>
                    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
                        <div class="form-group <?php echo (!empty($f_name_err)) ? 'has-error' : ''; ?>">
                            <label for="f_name">First Name</label>
                            <input type="text" name="f_name" id="f_name" class="form-control" value="<?php echo htmlspecialchars($f_name); ?>" placeholder="Enter first name">
                            <span class="help-block"><?php echo $f_name_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($l_name_err)) ? 'has-error' : ''; ?>">
                            <label for="l_name">Last Name</label>
                            <input type="text" name="l_name" id="l_name" class="form-control" value="<?php echo htmlspecialchars($l_name); ?>" placeholder="Enter last name">
                            <span class="help-block"><?php echo $l_name_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter email">
                            <span class="help-block"><?php echo $email_err; ?></span>
                        </div>
                        <input type="hidden" name="instructor_id" value="<?php echo htmlspecialchars($_SESSION["instructor_id"]); ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>