<?php
	session_start();
	//$currentpage="View Employees"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student DB</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
	<style type="text/css">
        .wrapper{
            width: 70%;
            margin:0 auto;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
		 $('.selectpicker').selectpicker();
    </script>
</head>
<body>
    <?php
        // Include config file
        require_once "config.php";
//		include "header.php";
	?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
		    <div class="page-header clearfix">
		     <h2> Sample Project CS 340 </h2> 
                       <p> Project should include CRUD operations. In this website you can:
				<ol> 	<li> CREATE new employess and  dependents </li>
					<li> RETRIEVE all dependents and prjects for an employee</li>
                                        <li> UPDATE employeee and dependent records</li>
					<li> DELETE employee and dependent records </li>
				</ol>
		       <h2 class="pull-left">Student Details</h2>
                        <a href="createStudent.php" class="btn btn-success pull-right">Add New Student</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Student Database
                    $sql = "SELECT student_id AS SID , f_name, l_name, number_of_classes, email
							FROM project_student";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=8%>SID</th>";
                                        echo "<th width=10%>First Name</th>";
                                        echo "<th width=10%>Last Name</th>";
                                        echo "<th width=10%>Number of Classes</th>";
										echo "<th width=10%>Email</th>";

                                        echo "<th width=10%>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['SID'] . "</td>";
                                        echo "<td>" . $row['f_name'] . "</td>";
                                        echo "<td>" . $row['l_name'] . "</td>";
										echo "<td>" . $row['number_of_classes'] . "</td>";									
										echo "<td>" . $row['email'] . "</td>";
                                        
                                        echo "<td>";
                                            echo "<a href='PROJ_CLASSES.php?SID=". $row['SID']."' title='View Class Schedule' data-toggle='tooltip'><span class='glyphicon glyphicon-th-list'></span></a>";
                                            echo "<a href='updateStudentDetails.php?Ssn=". $row['Ssn'] ."' title='Update Student Details' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='viewAssignments.php?Ssn=". $row['Ssn'] ."' title='View Assignents' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                            echo "<a href='dropClasses.php?Ssn=". $row['Ssn'] ."' title='Drop Classes' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
											echo "<a href='viewDependents.php?Ssn=". $row['Ssn']."&Lname=".$row['Lname']."' title='View Dependents' data-toggle='tooltip'><span class='glyphicon glyphicon-user'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }
					echo "<br> <h2> Courses </h2> <br>";
					
                    // Select Department Stats
					// You will need to Create a DEPT_STATS table
					
                    $sql2 = "SELECT * FROM COURSE";
                    if($result2 = mysqli_query($link, $sql2)){
                        if(mysqli_num_rows($result2) > 0){
                            echo "<div class='col-md-4'>";
							echo "<table width=30% class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=20%>Course Name</th>";
                                        echo "<th width = 20%>Course Number</th>";
                                        echo "<th width = 10%>Credits</th>";
                                        // echo "<th width = 40%>Department</th>";
	
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result2)){
                                    echo "<tr>";
                                        echo "<td>" . $row['Course_name'] . "</td>";
                                        echo "<td>" . $row['Course_number'] . "</td>";
                                        echo "<td>" . $row['Credit_hours'] . "</td>";
                                        // echo "<td>" . $row['Department'] . "</td>";
               
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result2);
                        } else{
                            echo "<p class='lead'><em>No records were found for Courses.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql2. <br>" . mysqli_error($link);
                    }
					
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>

</body>
</html>
