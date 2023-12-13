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

        if($status == "paid") {
            $_SESSION["shoppingcart"] = [];
        }
    } else {
        $result = null;
    }

?>

<?php if ($result !== null) { ?>
    <div class="container" style="width: 50%">
        <div class="bg-dark text-white p-4 my-5 mx-auto text-center">
            <?php
            $statusIcon = '';
            $statusMessage = '';

            switch ($status) {
                case 'open':
                    $statusIcon = '<object width="95" type="image/svg+xml" data="./Public/SVG/info.svg" class="logo"></object>';
                    $statusMessage = 'Transactie staat nog open<br><br> als u de transactie al betaald heeft kunt u refreshen om de status opnieuw op te halen.';
                    break;

                case 'canceled':
                    $statusIcon = '<object width="95" type="image/svg+xml" data="./Public/SVG/warning.svg" class="logo"></object>';
                    $statusMessage = 'Transactie is geannuleerd.';
                    break;

                case 'failed':
                    $statusIcon = '<object width="95" type="image/svg+xml" data="./Public/SVG/error.svg" class="logo"></object>';
                    $statusMessage = 'Transaction is niet succesvol. Probeer het opnieuw.';
                    break;

                case 'paid':
                    $statusIcon = '<object width="95" type="image/svg+xml" data="./Public/SVG/check.svg" class="logo"></object>';
                    $statusMessage = 'Uw transactie is succesvol.';
                    break;

                default:
                    $statusIcon = '<object width="95" type="image/svg+xml" data="./Public/SVG/info.svg" class="logo"></object>';
                    $statusMessage = 'Unknown transaction status.';
            }
            echo $statusIcon;
            ?>
            <h3 class="h4 font-weight-bold mb-4"><?php echo $statusMessage; ?></h3>
            
            <div class="mt-4">
                <a href="./shoppingcart.php" class="btn btn-primary btn-lg">Terug naar winkelmand</a>
            </div>
        </div>
    </div>
<?php } else { ?>
    <h1>Transaction not found.</h1>
<?php } ?>
