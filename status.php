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

<?php if($result !== null) { ?>
<div class="container" style="width: 50%">
    <div class="bg-dark text-white p-4 my-5 mx-auto text-center">
        <svg width="125" viewBox="0 0 24 24" class="text-success w-16 h-16 mx-auto my-6">
            <path fill="currentColor"
                d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z">
            </path>
        </svg>
        <h3 class="h4 font-weight-bold mb-4">Payment Done!</h3>
        <p class="text-gray-600 mb-2">Thank you for completing your secure online payment.</p>
        <p>Have a great day!</p>
        <div class="mt-4">
            <a href="#" class="btn btn-primary btn-lg">Back to shopping cart</a>
        </div>
    </div>
</div>
<?php } else { ?>
    <h1>Transactie is niet gevonden.</h1>
<?php } ?>