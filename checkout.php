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
        <?php include "Components/Checkout/customerInfo.php"; ?>
        <?php include "Components/Checkout/shoppingCartInfo.php"; ?>
    </div>
</form>
</div>

<?php include "footer.php"; ?>