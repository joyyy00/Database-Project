<!-- 
TODO:
- number of classes function to automatically calculate
- number_of_classes is set to 0 for new students for now
-->

<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$SID = $Fname = $Lname = $numClasses = $email = "";
$SID_err = $Fname_err = $Lname_err = $numClasses_err = $email_err= "" ;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate SID
    $SID = trim($_POST["SID"]);
    if(empty($SID)){
        $SID_err = "Please enter a SID (student ID).";     
    } elseif(!ctype_digit($SID)){
        $SID_err = "Please enter a positive integer value of SID.";
    } 

    // Validate First name
    $Fname = trim($_POST["Fname"]);
    if(empty($Fname)){
        $Fname_err = "Please enter a first name.";
    } elseif(!preg_match("/^[a-zA-Z\s]+$/", $Fname)){
        $Fname_err = "Invalid first name.";
    } 
    // Validate Last name
    $Lname = trim($_POST["Lname"]);
    if(empty($Lname)){
        $Lname_err = "Please enter a last name.";
    } elseif(!preg_match("/^[a-zA-Z\s]+$/", $Fname)){
        $Lname_err = "Invalid last name.";
    } 

	// Validate email
    $email = trim($_POST["email"]);
    if(empty($email)){
        $email_err = "Please enter an email address.";     		
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Invalid email address.";
    }
    
    // Check input errors before inserting in database
    if(empty($SID_err) && empty($Fname_err) && empty($Lname_err) 
				&& empty($emial_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO Project_Student (student_id, f_name, l_name, number_of_classes, email) 
		        VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            $numClasses = 0; // placeholder for now... need to implement function
            mysqli_stmt_bind_param($stmt, "issis", $SID, $Fname, $Lname, $numClasses, $email);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
				    header("location: index.php");
					exit();
            } else{
                echo "<div class='alert alert-danger'>Error: Student ID may already exist.</div>";
				$SID_err = "Enter a unique SID.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
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
                        <h2>New Student Form</h2>
                    </div>
                    <p>Please fill this form and submit to add a student to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="form-group <?php echo (!empty($SID_err)) ? 'has-error' : ''; ?>">
                            <label>SID</label>
                            <input type="text" name="SID" class="form-control" value="<?php echo $SID; ?>">
                            <span class="help-block"><?php echo $SID_err;?></span>
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