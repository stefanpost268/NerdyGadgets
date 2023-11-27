<?php

declare(strict_types=1);

// check if method is POST
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// check if content type is JSON and get data
$raw_post_data = file_get_contents("php://input");
$post = json_decode($raw_post_data, true);

// check if id is set
if(!isset($post["id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing id"]);
    exit;
}

// check if id is valid
$id = $post["id"];
if(substr($id, 0, 3) !== "tr_") {
    http_response_code(400);
    echo json_encode(["error" => "Invalid id"]);
    exit;
}

// check if id exists in database
require_once __DIR__ . "/../database.php";
loadenv("../.env");
$connection = connectToDatabase();
$statement = mysqli_prepare($connection, "SELECT * FROM Transaction WHERE transaction_id = ?");
mysqli_stmt_bind_param($statement, "s", $id);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
mysqli_stmt_close($statement);

// check if transaction exists
if(mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Transaction not found"]);
    exit;
}

// update transaction status in database
$statement = mysqli_prepare($connection, "UPDATE Transaction SET status = 'paid' WHERE transaction_id = ?");
mysqli_stmt_bind_param($statement, "s", $id);
mysqli_stmt_execute($statement);
mysqli_stmt_close($statement);





