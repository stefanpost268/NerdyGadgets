<?php
include 'header.php';

if (isset($_POST["productAmount"])) {
    if ($_POST["productAmount"] != $_SESSION["shoppingcart"]) {
        $items = $_POST["productAmount"];
        foreach ($items as $id => $amount) {
            if ($amount > 100) {
                $amount = 100;
            }
            if ($amount < 1) {
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

if (isset($_SESSION["shoppingcart"])) {
    $products = getShoppingCartItems($databaseConnection);
    $totalPrice = getTotalPriceShoppingCart($products);
}
?>

<h1 class="pb-5 text-4xl">Winkelwagen</h1>
<div class="min-h-screen flex justify-center">
    <div class="max-w-screen-2xl w-full">
        <div class="flex flex-wrap">
            <div class="w-full md:w-7/12 mb-4 md:mb-0">
                <form method="POST">
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-white">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Artikel</th>
                                    <th class="px-4 py-2">Prijs</th>
                                    <th class="px-4 py-2">Aantal</th>
                                    <th class="px-4 py-2">Subtotaal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)) : ?>
                                    <?php foreach ($products as $product) : ?>
                                        <tr>
                                            <td class="px-4 py-2">
                                                <div class="flex items-center">
                                                    <img width='100' src='<?php echo $product["image"]; ?>' class="rounded-md">
                                                    <div class="ml-3 text-red-500">
                                                        <p class="truncate w-40"><a href="./productpage.php?id=<?php echo $product["item"]["StockItemID"]; ?>"><?php echo $product["item"]["StockItemName"]; ?></a></p>
                                                        <p class="text-gray-400"><?php echo "Article ID: " . $product["item"]["StockItemID"]; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2">€<?php echo number_format($product["item"]["SellPrice"], 2); ?></td>
                                            <td class="px-4 py-2">
                                                <input type="number" max="100" min="0" name="productAmount[<?php echo $product["item"]["StockItemID"]; ?>]" value="<?php echo $product['amount']; ?>" onchange="this.form.submit()" class="w-16 rounded-md border py-1 px-2 text-black dark:text-white bg-gray-300 dark:bg-gray-700">
                                            </td>
                                            <td class="px-4 py-2">€ <?php echo number_format($product["subtotal"], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan='4' class="px-4 py-2">Winkelmand is leeg</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="w-full md:w-5/12">
                <div class="bg-gray-800 border border-white rounded-md p-4">
                    <h2 class="text-white mb-4 text-xl font-bold">Winkelmand</h2>
                    <div class="border border-white p-4 rounded-md">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-300">Subtotaal:</span>
                            <span class="text-white">€ <?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-300">Verzendkosten:</span>
                            <span class="text-white">€ 0.00</span> <!-- You can update this with actual shipping costs -->
                        </div>
                        <hr class="border-gray-600 my-2">
                        <div class="flex justify-between font-bold">
                            <span class="text-gray-300">Totaal:</span>
                            <span class="text-white">€ <?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        <button class="bg-blue-500 text-white mt-4 py-2 px-4 rounded-md w-full">
                            <?php if (empty($products)) { ?>
                                <a class="text-white" href="./browse.php">Ga naar de winkel</a>
                            <?php } else { ?>
                                <a class="text-white" href="./checkout.php">Bestelling plaatsen</a>
                            <?php } ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>