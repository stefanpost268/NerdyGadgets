<?php
include 'header.php';

if (!isset($_SESSION["shoppingcart"]) || empty($_SESSION["shoppingcart"])) {
    header("Location: ./shoppingcart.php");
}

$products = getShoppingCartItems($databaseConnection);
$totalPrice = getTotalPriceShoppingCart($products);
$shippingCost = getShippingCost($totalPrice);

if (!empty($_POST)) {
    include './Validator/CheckoutValidator.php';
    $checkoutValidator = new Validator\CheckoutValidator();

    $errors = $checkoutValidator->validate($products);

    if (empty($errors)) {
        include './Controller/CheckoutController.php';
        $checkoutController = new Controller\CheckoutController($databaseConnection);

        try {
            $checkoutController->getTransaction("Bestelling bij NerdyGadgets", $_POST, $totalPrice, $shippingCost, $databaseConnection);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<div class="container mx-auto mt-5 mb-5 p-8 rounded shadow-lg text-white bg-gradient-to-b from-gray-800 to-black --geist-foreground:#000"">
    <?php if (!empty($errors)) { ?>
        <div class=" alert alert-danger" role="alert">
    <?php foreach ($errors as $error) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>
</div>
<?php } ?>
<form method="POST" id="checkout-create">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">Voltooi uw bestelling</h1>
            <h2 class="text-xl">Vul uw gegevens in voordat u bestelt</h2>
        </div>
        <a class="btn btn-primary" href="./shoppingcart.php">Terug</a>
    </div>
    <hr class="my-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="text-white">
            <h3 class="text-2xl font-bold mb-4">Klant informatie</h3>
            <div class="mb-4">
                <label for="name" class="block text-sm">Naam</label>
                <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="name" id="name" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm">Email</label>
                <input type="email" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="email" id="email" required>
            </div>
            <h3 class="text-xl mt-4">Aflever adres</h3>
            <div class="mb-4">
                <label for="postalcode" class="block text-sm">Postcode</label>
                <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="postalcode" id="postalcode" pattern="\d{4} [A-Za-z]{2}" title="Voer een geldige postocode in (bijv, 8302 BB)" required>
            </div>
            <div class="mb-4">
                <label for="housenr" class="block text-sm">Huisnummer</label>
                <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="housenr" id="housenr" pattern=".*\d+.*" title="Voer een geldig huis nummer in" required>
            </div>
            <div class="mb-4">
                <label for="residence" class="block text-sm">Woonplaats</label>
                <input type="text" class="form-input w-full py-2 px-3 rounded bg-gray-700 text-white" name="residence" id="residence" required>
            </div>
        </div>
        <div class="text-white">
            <h3 class="text-2xl font-bold mb-4">Je bestelling</h3>
            <div class="border p-4 rounded bg-gray-700">
                <div class="flex justify-between items-center mb-3">
                    <p class="font-semibold">Product</p>
                    <p class="font-semibold">Subtotaal</p>
                </div>
                <?php foreach ($products as $product) { ?>
                    <div class="flex justify-between items-center mt-2 relative">
                        <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 badge rounded-full bg-red-500 text-white">
                            <?php echo $product["amount"]; ?>
                        </div>
                        <img width='75' src='<?php echo $product["image"]; ?>' class="img-thumbnail">
                        <div class="flex-grow-1 truncate ml-3">
                            <a href="./productpage.php?id=<?php print($product["item"]["StockItemID"]) ?>" class="text-blue-400 hover:underline"><?php echo $product["item"]["StockItemName"] ?></a>
                        </div>
                        <p class="text-sm">€<?php print(number_format(($product["item"]["SellPrice"] * $product["amount"]), 2)) ?></p>
                    </div>
                <?php } ?>
                <hr class="my-3">
                <div class="flex items-center justify-between">
                    <p class="font-semibold">Subtotaal:</p>
                    <p class="font-semibold">€<?php echo number_format(($totalPrice), 2); ?></p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="font-semibold">Verzendkosten:</p>
                    <p class="font-semibold">€<?php echo number_format(($shippingCost), 2); ?></p>
                </div>
                <hr class="my-3">
                <div class="flex items-center justify-between">
                    <p class="font-semibold">Totaal:</p>
                    <p class="font-semibold">€<?php echo number_format(($totalPrice + $shippingCost), 2); ?></p>
                </div>
                <p class="text-sm pb-2">Inclusief BTW</p>
                <button class="bg-blue-500 text-white py-3 px-4 rounded-md w-full">Afrekenen</button>
            </div>
        </div>
    </div>
</form>
</div>

<?php include "footer.php"; ?>