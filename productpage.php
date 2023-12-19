<!-- This file contains all the code for the page that displays a single product -->
<?php
include __DIR__ . "/header.php";

$lang = json_decode(file_get_contents("Lang/nl.json"))->productPage;

if(isset($_POST["articleid"]) && isset($_POST["amount"])) {
    $id = $_POST["articleid"];
} else {
    $id = $_GET["id"] ?? NULL;
}

$stockItem = getStockItem($id, $databaseConnection);

if($stockItem !== null) {
    $stockItemImage = getStockItemImage($id, $databaseConnection, $stockItem['BackupImagePath']);
}

$max = $stockItem['QuantityOnHand'] > 100 ? 100 : $stockItem['QuantityOnHand'];

// Add to shopping bag
if (isset($_POST["articleid"]) && isset($_POST["amount"])) {
    $amount = intval($_POST["amount"]);

    if (!isset($_SESSION["shoppingcart"])) {
        $_SESSION["shoppingcart"] = array();
    }

    if (isset($_SESSION["shoppingcart"][$id])) {
        if ($amount < 1) {
            unset($_SESSION["shoppingcart"][$id]);
            $status = "deleted";
            $succesfull = true;
        } else {
            $status = "updated";
            if($max < $amount) {
                $amount = $max;
            }

            $succesfull = $_SESSION["shoppingcart"][$id] = $amount;
        }
    } else {
        if ($amount >= 1) {
            if($max < $amount) {
                $amount = $max;
            }
            
            $status = "added";
            $succesfull = $_SESSION["shoppingcart"][$id] = $amount;
        }
    }
}

// Get current shopping bag amount of product.
if (isset($_SESSION["shoppingcart"][$id])) {
    $amount = $_SESSION["shoppingcart"][$id];
} else {
    $amount = 0;
}

?>

<main class="relative flex flex-col items-center justify-start w-full overflow-hidden min-h-screen bg-gradient-to-b from-gray-800 to-black --geist-foreground:#000">
    <div class="container mx-auto p-8 text-white">
        <?php if (isset($status) && isset($succesfull)) { include 'Components/Productpage/message.php'; } ?>

        <?php if($stockItem !== null) { ?>
            <?php if (isset($stockItem['Video'])) { ?>
                <div class="mb-8 h-96">
                    <?php print($stockItem['Video']); ?>
                </div>
            <?php } ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="col-span-1 md:col-span-2 lg:col-span-1">
                    <div id="controls-carousel" class="relative overflow-hidden">
                        <div class="h-96 bg-gradient-to-b from-gray-800 to-gray-900">
                            <?php foreach ($stockItemImage as $image) { ?>
                                <img src="<?php echo $image['ImagePath']; ?>" class="w-full h-full object-cover" alt="<?php print($stockItem['StockItemName']); ?>">
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-span-1 md:col-span-1 lg:col-span-1">
                    <h1 class="font-bold text-3xl mb-2"><?php echo $stockItem['StockItemName']; ?></h1>
                    <h2 class="mb-4 text-xl">Artikelnummer: <?php echo $stockItem["StockItemID"]; ?></h2>
                    <p class="mb-2"><?php printf("€ %.2f", $stockItem['SellPrice']) ?> <b>Inclusief BTW</b></p>
                    <p class="mb-4 text-green-500"><?php print(getVoorraadTekst($stockItem["QuantityOnHand"])); ?></p>
                    <form class="bg-gray-800 mt-5 p-4 rounded-lg" method="POST" action="productpage.php?id=<?php print($stockItem["StockItemID"]); ?>">
                        <div class="flex items-center mb-4">
                            <input type="hidden" name="articleid" value="<?php print($id); ?>">
                            <input class="border rounded-md px-3 py-2 w-full text-black bg-white focus:outline-none" type="number" id="amount" name="amount" value="<?php print($amount); ?>" min="0" max="<?php print($max); ?>">
                        </div>
                        <input type="submit" value="In winkelwagen" class="bg-blue-500 text-white px-6 py-2 rounded-md w-full hover:bg-blue-600 focus:outline-none transform transition-transform hover:scale-103" />
                    </form>
                </div>
                <div class="col-span-1">
                    <div id="productDescription" class="pt-5">
                        <h2 class="font-bold text-2xl mb-2">Product beschrijving</h2>
                        <p class="mb-4"><?php echo $stockItem['SearchDetails']; ?></p>
                    </div>
                    <div id="stockSpecs" class="pt-5">
                        <h2 class="font-bold text-2xl mb-2">Product specificaties</h2>
                        <?php
                        $customFields = json_decode($stockItem['CustomFields'], true);
                        if (is_array($customFields)) {
                        ?>
                            <ul class="list-disc pl-4">
                                <?php foreach ($customFields as $specName => $specText) { ?>
                                    <?php
                                    $specNameTranslated = isset($lang->stockSpecs->$specName) ? $lang->stockSpecs->$specName : $specName;
                                    if (!is_array($specText)) {
                                        echo "<li>{$specNameTranslated}: {$specText}</li>";
                                    } else if (count($specText) > 0) {
                                        echo "<li>{$specNameTranslated}: ";
                                        foreach ($specText as $specTextItem) {
                                            echo "{$specTextItem} ";
                                        }
                                        echo "</li>";
                                    }
                                    ?>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="flex flex-col items-center justify-center text-center bg-gray-800 p-8 rounded-lg shadow-md">
                <h1 class="text-white text-4xl font-bold mb-4">Product niet gevonden</h1>
                <img class="bg-white rounded-xl shadow-md" src="./Public/SVG/not-found.svg" alt="Empty Cart" width="150" height="150">
                <p class="text-gray-300 mt-4">
                    Helaas, we konden uw product niet vinden.
                </p>
                <button class="bg-blue-500 text-white px-6 py-2 mt-5 rounded-md hover:bg-blue-600 focus:outline-none transform transition-transform hover:scale-103" onclick="window.location = 'browse.php'">
                    Terug naar producten
                </button>
            </div>
        <?php } ?>
    </div>
</main>

