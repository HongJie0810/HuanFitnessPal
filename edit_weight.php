<?php
include('db_conn.php');
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $new_weight = $_POST['weight'];
    $new_date = $_POST['date'];

    $update_sql = "UPDATE weights SET weight = ?, date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_weight, $new_date, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}