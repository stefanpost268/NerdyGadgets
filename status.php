<?php
    include __DIR__ . "/header.php";

    $id = $_GET["id"] ?? 999999999;
    $statement = mysqli_prepare($databaseConnection, "SELECT * FROM Transaction WHERE id = ?");
    mysqli_stmt_bind_param($statement, "s", $id);
    mysqli_stmt_execute($statement);
    $ReturnableResult = mysqli_stmt_get_result($statement);
    mysqli_stmt_close($statement);

    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
        $status = $result["status"];
    } else {
        $result = null;
    }
?>

