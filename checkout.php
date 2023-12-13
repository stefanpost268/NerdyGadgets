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

<div class="container mt-5 mb-5">
    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($errors as $error) { ?>
                <p><?php echo $error; ?></p>
            <?php } ?>
        </div>
    <?php } ?>
    <form method="POST" id="checkout-create">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Voltooi uw bestelling</h1>
                <h2>Vul uw gegevens in voordat u bestelt</h2>
            </div>
            <a class="btn btn-primary" href="./shoppingcart.php">Terug</a>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Klant informatie</h3>
                <label for="name">Naam</label>
                <input type="text" class="form-control" name="name" id="name" required>
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" required>
                <h3 class="mt-3">Aflever adres</h3>
                <label for="postalcode">Postcode</label>
                <input type="text" class="form-control" name="postalcode" id="postalcode" pattern="\d{4} [A-Za-z]{2}" title="Voer een geldige postocode in (bijv, 8302 BB)" required>
                <label for="housenr">Huisnummer</label>
                <input type="text" class="form-control" name="housenr" id="housenr" pattern=".*\d+.*" title="Voer een geldig huis nummer in" required>
                <label for="residence">Woonplaats</label>
                <input type="text" class="form-control" name="residence" id="residence" required>
            </div>
            <div class="col-md-6">
                <h3>Je bestelling</h3>
                <div class="border p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <p>Product</p>
                        <p>Subtotaal</p>
                    </div>
                    <?php foreach ($products as $product) { ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="position-relative">
                                <img width='75' src='<?php echo $product["image"]; ?>' class="img-thumbnail">
                                <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white" style="margin-left: -15px; padding: 5px;">
                                    <?php echo $product["amount"]; ?>
                                </div>
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <a href="./productpage.php?id=<?php print($product["item"]["StockItemID"]) ?>"><?php echo $product["item"]["StockItemName"] ?></a>
                            </div>
                            <p>€<?php print(number_format(($product["item"]["SellPrice"] * $product["amount"]), 2)) ?></p>
                        </div>
                    <?php } ?>
                    <hr class="my-3">
                    <div class="d-flex">
                        <h3 class="mb-0">Totaal: €<?php echo number_format($totalPrice, 2); ?></h3>
                        <p class="ml-2">Inclusief BTW</p>
                    </div>
                    <button class="btn btn-primary mt-3" style="width: 100%">Afrekenen</button>
                </div>
            </div>
        </div>
    </form>
</div>