<?php
include 'header.php';

if (!isset($_SESSION["shoppingcart"]) || empty($_SESSION["shoppingcart"])) {
    header("Location: ./shoppingcart.php");
}

$products = getShoppingCartItems($databaseConnection);
$totalPrice = getTotalPriceShoppingCart($products);

if (!empty($_POST)) {
    include './Validator/CheckoutValidator.php';
    $checkoutValidator = new Validator\CheckoutValidator();

    $errors = $checkoutValidator->validate($products);

    if (empty($errors)) {
        include './Controller/CheckoutController.php';
        $checkoutController = new Controller\CheckoutController($databaseConnection);

        try {
            $checkoutController->getTransaction("Bestelling bij NerdyGadgets", $_POST, $totalPrice, $databaseConnection);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<div class="container mx-auto mt-5 mb-5 p-8 bg-gray-900 bg-gray-800 rounded shadow-lg text-white">
    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger" role="alert">
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
                <h3 class="text-xl">Je bestelling</h3>
                <div class="border p-3">
                    <div class="flex justify-between items-center">
                        <p>Product</p>
                        <p>Subtotaal</p>
                    </div>
                    <?php foreach ($products as $product) { ?>
                        <div class="flex justify-between items-center mt-2">
                            <div class="relative">
                                <img width='75' src='<?php echo $product["image"]; ?>' class="img-thumbnail">
                                <div class="absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white" style="margin-left: -15px; padding: 5px;">
                                    <?php echo $product["amount"]; ?>
                                </div>
                            </div>
                            <div class="flex-grow-1 truncate">
                                <a href="./productpage.php?id=<?php print($product["item"]["StockItemID"]) ?>"><?php echo $product["item"]["StockItemName"] ?></a>
                            </div>
                            <p>€<?php print(number_format(($product["item"]["SellPrice"] * $product["amount"]), 2)) ?></p>
                        </div>
                    <?php } ?>
                    <hr class="my-3">
                    <div class="flex">
                        <h3 class="mb-0">Totaal: €<?php echo number_format($totalPrice, 2); ?></h3>
                        <p class="ml-2">Inclusief BTW</p>
                    </div>
                    <button class="btn btn-primary mt-3" style="width: 100%">Afrekenen</button>
                </div>
            </div>
        </div>
    </form>
</div>