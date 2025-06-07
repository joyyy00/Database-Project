<!-- 
TODO:
- number of classes function to automatically calculate
- number_of_classes is set to 0 for new students for now
-->

<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$student_id = $Fname = $Lname = $email = "";
$student_id_err = $Fname_err = $Lname_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate student_id
    $student_id = trim($_POST["student_id"]);
    if (empty($student_id)) {
        $student_id_err = "Please enter a student ID.";
    } elseif (!ctype_digit($student_id)) {
        $student_id_err = "Student ID must be a positive integer.";
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
    if (empty($student_id_err) && empty($Fname_err) && empty($Lname_err) && empty($email_err)) {
        $sql = "INSERT INTO Project_Student (student_id, f_name, l_name, number_of_classes, email)
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            $numClasses = 0; // Default value
            mysqli_stmt_bind_param($stmt, "issis", $student_id, $Fname, $Lname, $numClasses, $email);

            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error: Student ID may already exist.</div>";
                $student_id_err = "Enter a unique student ID.";
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
    <title>Add New Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
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
                        <h2>New Student Form</h2>
                    </div>
                    <p>Please fill this form and submit to add a student to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($student_id_err)) ? 'has-error' : ''; ?>">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control" value="<?php echo $student_id; ?>">
                            <span class="help-block"><?php echo $student_id_err;?></span>
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