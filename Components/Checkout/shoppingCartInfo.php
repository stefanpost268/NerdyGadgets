<div class="text-white">
    <h3 class="text-2xl font-bold mb-4">Je bestelling</h3>
    <div class="border p-4 rounded bg-gray-700">
        <div class="flex justify-between items-center mb-3">
            <p class="font-semibold">Product</p>
            <p class="font-semibold">Subtotaal</p>
        </div>
        <?php foreach ($products as $product) { ?>
            <div class="flex justify-between items-center mt-2 relative">
                <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 badge rounded-full bg-red-500 text-white w-6 h-6 flex items-center justify-center">
                    <?php print($product["amount"]); ?>
                </div>
                <img width='75' src='<?php print($product["image"]); ?>' class="img-thumbnail">
                <div class="flex-grow-1 truncate ml-3">
                    <a href="./productpage.php?id=<?php print($product["item"]["StockItemID"]) ?>" class="text-blue-400 hover:underline">
                        <?php print($product["item"]["StockItemName"]); ?>
                    </a>
                </div>
                <p class="text-sm">€ <?php print(number_format(($product["item"]["SellPrice"] * $product["amount"]), 2, ',', '')) ?></p>
            </div>
        <?php } ?>
        <hr class="my-3">
        <div class="flex items-center justify-between">
            <p class="font-semibold">Subtotaal:</p>
            <p class="font-semibold">€ <?php print(number_format(($totalPrice), 2, ',', '')); ?></p>
        </div>
        <div class="flex items-center justify-between">
            <p class="font-semibold">Verzendkosten:</p>
            <p class="font-semibold">€ <?php print(number_format(($shippingCost), 2, ',', '')); ?></p>
        </div>
        <hr class="my-3">
        <div class="flex items-center justify-between">
            <p class="font-semibold">Totaal:</p>
            <p class="font-semibold">€ <?php print(number_format(($totalPrice + $shippingCost), 2, ',', '')); ?></p>
        </div>
        <p class="text-sm pb-2">Inclusief BTW</p>
        <button class="bg-blue-500 text-white py-3 px-4 rounded-md w-full">
            Afrekenen
        </button>
    </div>
</div>