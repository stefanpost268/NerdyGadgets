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

<?php if ($result !== null) { ?>
    <div class="container" style="width: 50%">
        <div class="bg-dark text-white p-4 my-5 mx-auto text-center">
            <?php
            $statusIcon = '';
            $statusMessage = '';

            switch ($status) {
                case 'open':
                    $statusIcon = '<svg width="100" viewBox="0 0 24 24" class="text-primary w-16 h-16 mx-auto my-6">
                                    <path fill="currentColor"
                                        d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z">
                                    </path>
                                </svg>';
                    $statusMessage = 'Transactie staat nog open<br><br> als u de transactie al betaald heeft kunt u refreshen om de status opnieuw op te halen.';
                    break;

                case 'canceled':
                    $statusIcon = '<svg width="100" viewBox="0 0 24 24" class="text-danger w-16 h-16 mx-auto my-6">
                                    <path fill="currentColor"
                                        d="M12,2C6.477,2,2,6.477,2,12s4.477,10,10,10,10-4.477,10-10S17.523,2,12,2Zm0,18a7.96,7.96,0,0,1-5.657-2.343,8.014,8.014,0,0,1,0-11.314,7.961,7.961,0,0,1,11.314,0,8.014,8.014,0,0,1,0,11.314A7.96,7.96,0,0,1,12,20ZM7.05,7.05a8.014,8.014,0,0,1,11.314,0,7.961,7.961,0,0,1,0,11.314A8.014,8.014,0,0,1,7.05,7.05Z">
                                    </path>
                                </svg>';
                    $statusMessage = 'Transactie is geannuleerd.';
                    break;

                case 'failed':
                    $statusIcon = '<svg width="100" viewBox="0 0 24 24" class="text-warning w-16 h-16 mx-auto my-6">
                                    <path fill="currentColor"
                                        d="M12,2C6.477,2,2,6.477,2,12s4.477,10,10,10,10-4.477,10-10S17.523,2,12,2Zm0,18a7.96,7.96,0,0,1-5.657-2.343,8.014,8.014,0,0,1,0-11.314,7.961,7.961,0,0,1,11.314,0,8.014,8.014,0,0,1,0,11.314A7.96,7.96,0,0,1,12,20ZM7.05,7.05a8.014,8.014,0,0,1,11.314,0,7.961,7.961,0,0,1,0,11.314A8.014,8.014,0,0,1,7.05,7.05Z">
                                    </path>
                                </svg>';
                    $statusMessage = 'Transaction is niet successfull. Probeer het opnieuw.';
                    break;

                case 'paid':
                    $statusIcon = '<svg width="100" viewBox="0 0 24 24" class="text-success w-16 h-16 mx-auto my-6">
                                    <path fill="currentColor"
                                        d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z">
                                    </path>
                                </svg>';
                    $statusMessage = 'Uw transactie is successfull.';
                    break;

                default:
                    $statusMessage = 'Unknown transaction status.';
            }
            echo $statusIcon;
            ?>
            <h3 class="h4 font-weight-bold mb-4"><?php echo $statusMessage; ?></h3>
            
            <div class="mt-4">
                <a href="./shoppingcart.php" class="btn btn-primary btn-lg">Back to shopping cart</a>
            </div>
        </div>
    </div>
<?php } else { ?>
    <h1>Transaction not found.</h1>
<?php } ?>
