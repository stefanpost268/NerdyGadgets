<?php

declare(strict_types=1);

use Service\HTTP;
use Controller\CheckoutController;

// check if method is POST
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// check if content type is JSON and get data
$raw_post_data = file_get_contents("php://input");
$values = explode("=", $raw_post_data);

if($values[0] !== "id" || !isset($values[1])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid data"]);
    exit;
}

// check if id is valid
$id = $values[1];
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

// check if database transaction exists
if(mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Transaction not found"]);
    exit;
}

// Get transacion status.
require_once __DIR__ . "/../Service/http.php";
require_once __DIR__ . "/../Controller/CheckoutController.php";

$molliePayment = HTTP::get(CheckoutController::MOLLIE_URL."payments/".$id, [
    "Content-Type: application/json",
    "Authorization: Bearer ".$_ENV["MOLLIE_API_KEY"]
]);

// check if mollie transaction exists
if(!isset($molliePayment["response"]->id)) {
    http_response_code(404);
    echo json_encode(["error" => "Transaction not found"]);
    exit;
}

$mollieTransaction = $molliePayment["response"];

// update transaction status in database
$statement = mysqli_prepare($connection, "UPDATE Transaction SET status = '$mollieTransaction->status' WHERE transaction_id = '$mollieTransaction->id'");
mysqli_stmt_execute($statement);
mysqli_stmt_close($statement);