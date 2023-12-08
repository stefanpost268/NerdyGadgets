<!-- This file contains all the code for the page that displays a single product -->
<?php
include __DIR__ . "/header.php";

// Add to shopping bag
if (isset($_POST["articleid"]) && isset($_POST["amount"])) {
    $amount = intval($_POST["amount"]);

    if (!isset($_SESSION["shoppingcart"])) {
        $_SESSION["shoppingcart"] = array();
    }

    $id = $_POST["articleid"];
    if (isset($_SESSION["shoppingcart"][$id])) {
        if ($amount < 1) {
            unset($_SESSION["shoppingcart"][$id]);
            $status = "deleted";
            $succesfull = true;
        } else {
            $status = "updated";
            $succesfull = $_SESSION["shoppingcart"][$id] = $amount;
        }
    } else {
        if ($amount >= 1) {
            $status = "added";
            $succesfull = $_SESSION["shoppingcart"][$id] = $amount;
        }
    }
} else {
    $id = $_GET["id"] ?? NULL;
}

// Get current shopping bag amount of product.
if (isset($_SESSION["shoppingcart"][$id])) {
    $amount = $_SESSION["shoppingcart"][$id];
} else {
    $amount = 0;
}

$stockItem = getStockItem($id, $databaseConnection);
$stockItemImage = getStockItemImage($id, $databaseConnection, $stockItem['BackupImagePath']);
?>


<div class="container mx-auto p-8">

    <?php 
        if(isset($status) && isset($succesfull)) {
            include 'Components/Productpage/message.php';
        }
    ?>

    <?php if (isset($stockItem['Video'])) { ?>
        <div class="mb-8 h-72">
            <?php print($stockItem['Video']); ?>
        </div>
    <?php } ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Product Images -->
        <div class="col-span-1 md:col-span-2 lg:col-span-1">
            <div id="controls-carousel" class="relative" data-carousel="static">
                <div class="relative h-96 overflow-hidden rounded-lg bg-gradient-to-b from-gray-800 to-gray-900">
                    <?php for ($i = 0; $i < count($stockItemImage); $i++) { ?>
                        <img src="<?php echo $stockItemImage[$i]['ImagePath']; ?>" class="w-full h-full object-cover" alt="<?php print($stockItem['StockItemName']); ?>">
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-span-1 md:col-span-1 lg:col-span-1 text-white">

            <h1 class="font-bold text-3xl mb-2"><?php echo $stockItem['StockItemName']; ?></h1>
            <h2 class="mb-4 text-xl">Artikelnummer: <?php echo $stockItem["StockItemID"]; ?></h2>
            <p class="mb-2"><?php printf("€ %.2f", $stockItem['SellPrice']) ?> <b>Inclusief BTW</b></p>
            <p class="mb-4"><?php print(getVoorraadTekst($stockItem["QuantityOnHand"])); ?></p>

            <form class="bg-gray-800 mt-5 p-4 rounded-lg" method="POST" action="productpage.php?id=<?php print($stockItem["StockItemID"]); ?>">
                <div class="flex items-center mb-4">
                    <input type="hidden" name="articleid" value="<?php print($id); ?>">
                    <input
                        class="border rounded-md px-3 py-2 w-full text-black bg-white focus:outline-none"
                        type="number"
                        id="amount"
                        name="amount"
                        value="<?php print($amount); ?>"
                        min="0"
                        max="<?php print($stockItem['QuantityOnHand'] > 100 ? 100 : $stockItem['QuantityOnHand']); ?>"
                    >
                </div>
                <input type="submit" value="In winkelwagen" class="bg-blue-500 text-white px-6 py-2 rounded-md w-full hover:bg-blue-600 focus:outline-none"/>
            </form>
        </div>

        <div class="col-span-1 text-white">

            <div id="productDescription">
                <h2 class="font-bold text-2xl mb-2">Product beschrijving</h2>
                <p class="mb-4"><?php echo $stockItem['SearchDetails']; ?></p>
            </div>

            <div id="stockSpecs" class="pt-5">
                <h2 class="font-bold text-2xl mb-2">Product specificaties</h2>
                <?php
                $CustomFields = json_decode($stockItem['CustomFields'], true);
                if (is_array($CustomFields)) {
                ?>
                    <!-- Product Specifications -->
                    <ul class="list-disc pl-4 dark">
                        <?php foreach ($CustomFields as $specName => $specText) { ?>
                            <?php
                            if (is_array($specText)) {
                                foreach ($specText as $subText) {
                                    echo "<li>{$specName}: {$subText}</li>";
                                }
                            } else {
                                echo "<li>{$specName}: {$specText}</li>";
                            }
                            ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>
</div>