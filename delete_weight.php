<?php
include('db_conn.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $sql = "DELETE FROM weights WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: weight.php"); 
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>