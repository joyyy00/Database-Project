<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$instructor_id = $Fname = $Lname = $email = "";
$instructor_id_err = $Fname_err = $Lname_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate instructor_id
    $instructor_id = trim($_POST["instructor_id"]);
    if (empty($instructor_id)) {
        $instructor_id_err = "Please enter an instructor ID.";
    } elseif (!ctype_digit($instructor_id)) {
        $instructor_id_err = "Instructor ID must be a positive integer.";
    }

    // Validate First name
    $Fname = trim($_POST["Fname"]);
    if (empty($Fname)) {
        $Fname_err = "Please enter a first name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $Fname)) {
        $Fname_err = "Invalid first name.";
    }

    // Validate Last name
    $Lname = trim($_POST["Lname"]);
    if (empty($Lname)) {
        $Lname_err = "Please enter a last name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $Lname)) {
        $Lname_err = "Invalid last name.";
    }

    // Validate email
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $email_err = "Please enter an email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email address.";
    }

    // Check input errors before inserting in database
    if (empty($instructor_id_err) && empty($Fname_err) && empty($Lname_err) && empty($email_err)) {
        $sql = "INSERT INTO Project_Instructor (instructor_id, f_name, l_name, email)
                VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isss", $instructor_id, $Fname, $Lname, $email);

            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error: Instructor ID may already exist.</div>";
                $instructor_id_err = "Enter a unique instructor ID.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>

 
<!-- Uses a HTML form (POST) submission -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Instructor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <!-- Link to Style -->
    <link rel="stylesheet" href="css/addStudentInstructor.css">
    <style type="text/css">
        .wrapper { width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>New Instructor Form</h2>
                    </div>
                    <p>Please fill this form and submit to add an instructor to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($instructor_id_err)) ? 'has-error' : ''; ?>">
                            <label>Instructor ID</label>
                            <input type="text" name="instructor_id" class="form-control" value="<?php echo $instructor_id; ?>">
                            <span class="help-block"><?php echo $instructor_id_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Fname_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="Fname" class="form-control" value="<?php echo $Fname; ?>">
                            <span class="help-block"><?php echo $Fname_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Lname_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="Lname" class="form-control" value="<?php echo $Lname; ?>">
                            <span class="help-block"><?php echo $Lname_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                            <span class="help-block"><?php echo $email_err;?></span>
                        </div>            
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
