<!-- 
TODO:
Student table
- instead of drop classes, maybe replace it with delete student (?)
- Move drop classes to view class schedule action

Instructor table
- add Instructor button
- create actions
-->


<?php
	session_start();
	//$currentpage="View Employees"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Canvas DB</title>

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
        //include "header.php";
	?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
		            <div class="page-header clearfix">
		                <h2> CS340 Project Mini Canvas Database </h2> 
                        <p> Project should include CRUD operations. In this website you can:
                        <ol> 	
                            <li> CREATE new students, instructors, assignments and classes</li>
                            <li> RETRIEVE all classes, and assignments for a student & classes for an instructor</li>
                            <li> UPDATE student and instructor records</li>
                            <li> DELETE students and instructor records</li>
                        </ol>
		                <h2 class="pull-left">Student Details</h2>
                        <a href="addStudent.php" class="btn btn-success pull-right">Add New Student</a>
                    </div>
                <?php
                // Include config file
                require_once "config.php";
                
                // Student Database
                $sql = "SELECT student_id AS SID , f_name, l_name, number_of_classes, email
                        FROM Project_Student";
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
                                        echo "<a href='viewSchedule.php?student_id=". $row['SID']."' title='View Class Schedule' data-toggle='tooltip'><span class='glyphicon glyphicon-th-list'></span></a>";
                                        echo "<a href='viewAssignments.php?student_id=". $row['SID'] ."' title='View Assignents' data-toggle='tooltip'><span class='glyphicon glyphicon-book'></span></a>";
                                        echo "<a href='updateStudentDetails.php?student_id=". $row['SID'] ."' title='Update Student Details' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                        echo "<a href='dropClasses.php?student_id=". $row['SID'] ."' title='Drop Classes' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
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

                // Instructor Database
                echo "<br> <h2> Instructor Details</h2> <br>";
                // Select Intructor 
                $sql2 = "SELECT instructor_id AS ID, f_name, l_name, email FROM Project_Instructor";
                if($result2 = mysqli_query($link, $sql2)){
                    if(mysqli_num_rows($result2) > 0){
                        echo "<div class='col-md-4'>";
                        echo "<table width=30% class='table table-bordered table-striped'>";
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th width = 100%>Instructor ID</th>";
                                    echo "<th width = 20%>First Name</th>";
                                    echo "<th width = 10%>Last Name</th>";
                                    echo "<th width = 10%>Email</th>";
                                    echo "<th width = 30%>Action</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while($row = mysqli_fetch_array($result2)){
                                echo "<tr>";
                                    echo "<td>" . $row['ID'] . "</td>";
                                    echo "<td>" . $row['f_name'] . "</td>";
                                    echo "<td>" . $row['l_name'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";

                                    echo "<td>";
                                        echo "<a href='viewClasses.php?instructor_id=". $row['ID']."' title='View Classes' data-toggle='tooltip'><span class='glyphicon glyphicon-th-list'></span></a>";
                                        // In view classes, show class details such as students, class days, etc
                                        // update class button to delete or reassign class

                                        echo "<a href='updateInstructorDetails.php?instructor_id=". $row['ID'] ."' title='Update Instructor Details' data-toggle='tooltip'><span class='glyphicon glyphicon-book'></span></a>";
                                        echo "<a href='PLACEHOLDER.php?instructor_id=". $row['ID'] ."' title='PLACEHOLDER' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                        echo "<a href='PLACEHOLDER.php?instructor_id=". $row['ID'] ."' title='PLACEHOLDER' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
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
            </div>
        </div>
    </div>
</body>
</html>
