<?php
include 'header.php';
$config = json_decode(file_get_contents("Config/main.json"));
$lang = json_decode(file_get_contents("Lang/nl.json"))->shoppingCart;

if (isset($_POST["productAmount"])) {
    if ($_POST["productAmount"] != $_SESSION["shoppingcart"]) {
        $items = $_POST["productAmount"];
        foreach($items as $id => $amount) {
            if($amount > $config->maxInShoppingBasket) { $amount = $config->maxInShoppingBasket; }
            if($amount < 1) {
                unset($_SESSION["shoppingcart"][$id]);
            } else if (is_numeric($amount)) {
                $_SESSION["shoppingcart"][$id] = round($amount);
            } else {
                $_SESSION["shoppingcart"][$id] = 1;
            }
        }
    }
}

$products = [];
$totalPrice = 0;
$shippingCost = 0;

if (isset($_SESSION["shoppingcart"])) {
    $products = getShoppingCartItems($databaseConnection);
    $totalPrice = getTotalPriceShoppingCart($products);
    $shippingCost = getShippingCost($totalPrice);
}
?>

<div class="container mx-auto p-4">
    <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <div id="products" class="w-full md:w-7/12 overflow-y-hidden md:overflow-y-auto md:max-h-screen">
            <form method="POST">
                <?php if (!empty($products)) : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($products as $product) : ?>
                            <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white p-4 rounded-md shadow-md mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <img width='150' src='<?php echo $product["image"]; ?>' class="rounded-md">
                                    </div>
                                    <div class="ml-4 flex-grow">
                                        <p class="text-blue-500 text-lg font-semibold">
                                            <a href="./productpage.php?id=<?php echo $product["item"]["StockItemID"]; ?>" class="hover:underline">
                                                <?php echo $product["item"]["StockItemName"]; ?>
                                            </a>
                                        </p>
                                        <p class="text-gray-400"><?php print($lang->articleId.": " . $product["item"]["StockItemID"]); ?></p>
                                        <p class="text-green-500 font-semibold text-xl">€<?php echo number_format($product["item"]["SellPrice"], 2); ?></p>
                                        <div class="flex items-center mt-2">
                                            <label class="mr-2 text-gray-400"><?php print($lang->quantity); ?>:</label>
                                            <input type="number" max="100" min="0" name="productAmount[<?php echo $product["item"]["StockItemID"]; ?>]" value="<?php echo $product['amount']; ?>" onchange="this.form.submit()" class="w-16 rounded-md border py-1 px-2 text-white bg-gray-700 text-black bg-gray-300">
                                        </div>
                                        <p class="text-green-500 font-semibold mt-2"><?php print($lang->subtotal); ?>: € <?php echo number_format($product["subtotal"], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <!-- Empty Cart Message -->
                    <div class="flex flex-col items-center justify-center text-center bg-gray-800 p-8 rounded-lg shadow-md">
                        <h1 class="text-white text-4xl font-bold mb-4"><?php print($lang->emptyCart); ?></h1>
                        <img class="bg-white rounded-xl shadow-md" src="./Public/SVG/shopping-cart-empty.svg" alt="Empty Cart" width="150" height="150">
                        <p class="text-gray-300 mt-4"><?php print($lang->emptyCartDescription1); ?></p>
                        <p class="text-gray-300 pt-5 md:pt-0"><?php print($lang->emptyCartDescription2); ?></p>
                        <a href="./browse.php" class="text-blue-500 underline mt-4 hover:text-blue-700"><?php print($lang->continueShopping); ?></a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Shopping Cart Info -->
        <div id="shoppingcartinfo" class="w-full md:w-5/12">
            <div class="bg-gray-800 text-white p-4 rounded-md">
                <h2 class="text-xl font-bold mb-4"><?php print($lang->shoppingCardTitle); ?></h2>
                <?php if (!empty($products)) : ?>
                    <div class="mb-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-300"><?php print($lang->subtotal); ?>:</span>
                            <span class="text-white">€ <?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-300"><?php print($lang->shippingcost); ?>:</span>
                            <span class="text-white">€ <?php echo number_format($shippingCost, 2); ?></span>
                        </div>
                        <hr class="border-gray-600 my-2">
                        <div class="flex justify-between font-bold">
                            <span class="text-gray-300"><?php print($lang->total); ?>:</span>
                            <span class="text-white">€ <?php echo number_format(($totalPrice + $shippingCost), 2); ?></span>
                        </div>
                    </div>
                    <button class="bg-blue-500 text-white py-2 px-4 rounded-md w-full">
                        <a class="text-white" href="<?php echo empty($products) ? './browse.php' : './checkout.php'; ?>">
                            <?php print($lang->toCheckout); ?>
                        </a>
                    </button>
                <?php else : ?>
                    <p><?php print($lang->emptyCart); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>