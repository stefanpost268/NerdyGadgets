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

    if ($status == "paid") {
        $_SESSION["shoppingcart"] = [];
    }
} else {
    $result = null;
}

?>

<?php if ($result !== null) { ?>
    <div class="container mx-auto w-full p-4 md:w-1/2 md:pt-8">
        <div class="bg-gradient-to-br from-gray-800 via-gray-900 to-gray-800 text-white p-6 md:p-8 my-8 mx-auto text-center rounded-lg shadow-lg">
            <?php
            $statusIcon = '';
            $statusMessage = '';
            $additionalInfo = '';

            switch ($status) {
                case 'open':
                    $statusIcon = '<object width="70" type="image/svg+xml" data="./Public/SVG/info.svg" class="logo mx-auto"></object>';
                    $statusMessage = 'Transactie staat nog open<br><br> Als u de transactie al heeft betaald, kunt u vernieuwen om de status opnieuw op te halen.';
                    $additionalInfo = '<p class="text-sm text-gray-400 mt-4">Wacht alstublieft even en vernieuw de pagina om de laatste status te controleren.</p>';
                    break;

                case 'canceled':
                    $statusIcon = '<object width="70" type="image/svg+xml" data="./Public/SVG/warning.svg" class="logo mx-auto"></object>';
                    $statusMessage = 'Transactie is geannuleerd.';
                    break;

                case 'failed':
                    $statusIcon = '<object width="70" type="image/svg+xml" data="./Public/SVG/error.svg" class="logo mx-auto"></object>';
                    $statusMessage = 'Betaling is niet succesvol verwerkt. Probeer het opnieuw of neem contact op met de klantenservice.';
                    break;

                case 'paid':
                    $statusIcon = '<object width="70" type="image/svg+xml" data="./Public/SVG/check.svg" class="logo mx-auto"></object>';
                    $statusMessage = '<p class="pt-3 md:pt-5">Uw transactie is succesvol voltooid.</p>';
                    $additionalInfo = '<p class="text-sm text-green-400 mt-2 md:mt-4">Bedankt voor uw betaling, u ontvangt binnenkort uw product!</p>';
                    break;

                default:
                    $statusIcon = '<object width="70" type="image/svg+xml" data="./Public/SVG/info.svg" class="logo mx-auto"></object>';
                    $statusMessage = 'Helaas, we konden de transactie niet vinden. Controleer de gegevens en probeer het opnieuw.';
            }
            echo $statusIcon;
            ?>
            <h3 class="text-lg md:text-xl font-bold mb-4"><?php echo $statusMessage; ?></h3>

            <?php echo $additionalInfo; ?>

            <div class="mt-4 md:mt-8">
                <a href="./shoppingcart.php" class="bg-blue-500 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg inline-block hover:bg-blue-600 transition duration-300">Terug naar winkelmand</a>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="relative flex flex-col p-6 md:p-10 items-center justify-start w-full overflow-hidden min-h-screen bg-gradient-to-b from-gray-800 to-black --geist-foreground:#000 text-center	">
        <h1 class="text-lg md:text-3xl font-bold text-red-500">Transactie niet gevonden</h1>
        <p class="mt-2 md:mt-4 text-gray-300">Helaas, we konden de opgegeven transactie niet vinden. Controleer de gegevens en probeer het opnieuw.</p>
    </div>
<?php } ?>
