<?php
require_once "config.php";

if (isset($_GET["assignment_id"]) && isset($_GET["class_id"])) {
    $assignment_id = $_GET["assignment_id"];
    $class_id = $_GET["class_id"];
    $instructor_id = isset($_GET["instructor_id"]) ? $_GET["instructor_id"] : null;

    $sql = "DELETE FROM Project_Assignment WHERE assignment_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $assignment_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: viewInstructorAssignments.php?class_id=$class_id" . ($instructor_id ? "&instructor_id=$instructor_id" : ""));
            exit();
        } else {
            echo "Error deleting assignment.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "Missing parameters.";
}
mysqli_close($link);
?>
