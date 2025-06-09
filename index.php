<!-- 
TODO:
- make css files for other files like class statistics, add forms, etc.

Student table
- Moved drop classes to viewSchedule
-->


<?php
	session_start();
	//$currentpage="View Students & Instructors"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Canvas DB</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Link CSS -->
    <link rel="stylesheet" href="css/mainPage.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>   
</head>

<body>
    <?php require_once "config.php"; ?>

    <div class="wrapper">
        <div class="container-fluid">
        <div class="page-header" style="display: flex; align-items: center; gap: 20px;">
            <img src="images/logo.png" alt="Mini Canvas Logo" style="height: 60px;">
            <div class="header-text">
                <h2>CS340 Project: Mini Canvas Database</h2>
                <p>This project demonstrates CRUD operations:</p>
                <ol>
                    <li>CREATE students, instructors, assignments, and classes</li>
                    <li>RETRIEVE class & assignment info for students and instructors</li>
                    <li>UPDATE student and instructor records</li>
                    <li>DELETE student and instructor records</li>
                </ol>
            </div>
        </div>

            <!-- Class Statistics Button -->
            <a href="classStats.php" class="btn btn-classStats btn-margin" style="background-color: #28a745; border-color: #28a745; color: white;">View Class Statistics</a>

            <!-- Student Database -->
            <div class="section-card">
                <div class="student-header clearfix"> 
                    <h2 class="pull-left">Student Details</h2>
                    <a href="addStudent.php" class="btn btn-success pull-right">Add New Student</a>
                </div>

                <?php
                $sql = "SELECT student_id AS SID , f_name, l_name, number_of_classes, email FROM Project_Student";
                if ($result = mysqli_query($link, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table class='table table-bordered table-striped'>";
                        echo "<thead><tr>
                                <th>SID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Number of Classes</th>
                                <th>Email</th>
                                <th>Action</th>
                              </tr></thead><tbody>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>
                                    <td>{$row['SID']}</td>
                                    <td>{$row['f_name']}</td>
                                    <td>{$row['l_name']}</td>
                                    <td>{$row['number_of_classes']}</td>
                                    <td>{$row['email']}</td>
                                    <td>
                                        <a href='viewSchedule.php?student_id={$row['SID']}' title='View Schedule' data-toggle='tooltip'><span class='glyphicon glyphicon-th-list'></span></a>
                                        <a href='viewAssignments.php?student_id={$row['SID']}' title='View Assignments' data-toggle='tooltip'><span class='glyphicon glyphicon-book'></span></a>
                                        <a href='updateStudentDetails.php?student_id={$row['SID']}' title='Edit' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>
                                        <a href='deleteStudent.php?student_id={$row['SID']}' title='Delete' data-toggle='tooltip'><span class='glyphicon glyphicon-remove'></span></a>
                                    </td>
                                  </tr>";
                        }
                        echo "</tbody></table>";
                        mysqli_free_result($result);
                    } else {
                        echo "<p class='lead'><em>No student records found.</em></p>";
                    }
                } else {
                    echo "ERROR: Could not execute $sql. " . mysqli_error($link);
                }
                ?>
            </div>

            <!-- Instructor Database -->
            <div class="section-card">
                <div class="instructor-header clearfix">
                    <h2 class="pull-left">Instructor Details</h2>
                    <a href="addInstructor.php" class="btn btn-success pull-right">Add New Instructor</a>
                </div>

                <?php
                $sql2 = "SELECT instructor_id AS ID, f_name, l_name, email FROM Project_Instructor";
                if ($result2 = mysqli_query($link, $sql2)) {
                    if (mysqli_num_rows($result2) > 0) {
                        echo "<table class='table table-bordered table-striped'>";
                        echo "<thead><tr>
                                <th>Instructor ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Action</th>
                              </tr></thead><tbody>";
                        while ($row = mysqli_fetch_array($result2)) {
                            echo "<tr>
                                    <td>{$row['ID']}</td>
                                    <td>{$row['f_name']}</td>
                                    <td>{$row['l_name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>
                                        <a href='viewInstructorSchedule.php?instructor_id={$row['ID']}' title='View Classes' data-toggle='tooltip'><span class='glyphicon glyphicon-th-list'></span></a>
                                        <a href='updateInstructorDetails.php?instructor_id={$row['ID']}' title='Edit' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>
                                        <a href='deleteInstructor.php?instructor_id={$row['ID']}' title='Delete' data-toggle='tooltip'><span class='glyphicon glyphicon-remove'></span></a>
                                    </td>
                                  </tr>";
                        }
                        echo "</tbody></table>";
                        mysqli_free_result($result2);
                    } else {
                        echo "<p class='lead'><em>No instructor records found.</em></p>";
                    }
                } else {
                    echo "ERROR: Could not execute $sql2. " . mysqli_error($link);
                }

                mysqli_close($link);
                ?>
            </div>
        </div>
    </div>
</body>
</html>
