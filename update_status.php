<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");

    $result = $conn->query("SELECT orders.*, users.name, users.email FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = '$order_id'");
    if ($result && $order = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'order' => $order
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}