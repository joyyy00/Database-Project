<?php
	session_start();
    // Include config file
    require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Class Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
	   <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">View Class Schedule</h2>
                        // Add Class button at top, WORK ON LATER
						<a href="addClass.php" class="btn btn-success pull-right">Add Class</a>
                    </div>
<?php

// Check existence of id parameter before processing further
if(isset($_GET["SID"]) && !empty(trim($_GET["SID"]))){
	$_SESSION["SID"] = $_GET["SID"];
}
if(isset($_GET["l_name"]) && !empty(trim($_GET["l_name"]))){
	$_SESSION["l_name"] = $_GET["l_name"];
}

if(isset($_SESSION["SID"]) ){
	
    // Prepare a select statement
    $sql = "SELECT class_id AS ClassID, class_name AS ClassName, date, time, location, instructor_id AS InstructorID 
            FROM Project_Class AS c
            JOIN Project_Attends AS a ON c.ClassID=a.class_id
            JOIN Project_Student AS s ON a.student_id=s.student_id
            WHERE s.student_id = ?";
  
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_Ssn);      
        // Set parameters
       $param_SID = $_SESSION["SID"];
	   $Lname = $_SESSION["l_name"];

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
			echo"<h4> Class Schedule for ".$Lname." &nbsp      SID =".$param_SID."</h4><p>";
			if(mysqli_num_rows($result) > 0){
				echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th width = 20%>Class ID</th>";
                            echo "<th>Class Name</th>";
                            echo "<th>Date</th>";
                            echo "<th>Time</th>";
                            echo "<th>Location</th>";
                            echo "<th>Instructor ID</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";							
				// output data of each row
                    while($row = mysqli_fetch_array($result)){
                        echo "<tr>";
                            echo "<td>" . $row['ClassID'] . "</td>";
                            echo "<td>" . $row['ClassName'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>" . $row['time'] . "</td>";
                            echo "<td>" . $row['location'] . "</td>";
                            echo "<td>" . $row['time'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";                            
                echo "</table>";				
				mysqli_free_result($result);
			} else {
				echo "No Projects. ";
			}
//				mysqli_free_result($result);
        } else{
			// URL doesn't contain valid id parameter. Redirect to error page
            header("location: error.php");
            exit();
        }
    }     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>					                 					
	<p><a href="index.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>