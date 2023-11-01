<?php
include 'header.php';

function getProductImage($id, $databaseConnection, $item): string
{
    $stockImage = getStockItemImage($id, $databaseConnection);

    if (isset($stockImage[0]["ImagePath"])) {
        return "Public/StockItemIMG/" . getStockItemImage($id, $databaseConnection)[0]["ImagePath"];
    } else {
        return "Public/StockGroupIMG/" . $item["BackupImagePath"];
    }
}

// Check if the "Clear Session" button was clicked
if (isset($_POST['clear_session'])) {
    // Clear the session
    session_unset();
}

$products = [];
$totalPrice = 0;

if (isset($_SESSION["shoppingcart"])) {
    foreach ($_SESSION["shoppingcart"] as $id => $amount) {
        $item = getStockItem($id, $databaseConnection);
        $imagePath = getProductImage($id, $databaseConnection, $item);
        $subtotal = round($amount * $item['SellPrice'], 2);

        $products[] = [
            "item" => $item,
            "image" => $imagePath,
            "amount" => $amount,
            'subtotal' => $subtotal,
        ];

        $totalPrice += $subtotal;
    }
}
?>

<h1>Winkelwagen</h1>
<div style="padding: 25px; display:flex;">
    <div class="table-left">
        <table>
            <thead>
                <tr>
                    <th>Artikel</th>
                    <th>Prijs</th>
                    <th>Aantal</th>
                    <th>Subtotaal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)) : ?>
                    <?php foreach ($products as $product) : ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <img width='100' src='<?php echo $product["image"]; ?>'>
                                    <div style="margin-left: 10px;">
                                        <?php
                                        echo $product["item"]["StockItemName"];
                                        echo "<br>";
                                        echo "Article ID: " . $product["item"]["StockItemID"];
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td>€ <?php echo number_format($product["item"]["SellPrice"], 2); ?></td>
                            <td><?php echo $product["amount"]; ?></td>
                            <td>€ <?php echo number_format($product["subtotal"], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan='2'>Shopping cart is empty</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <form method="post">
        <button type="submit" name="clear_session">Clear Session</button>
        </form>
    </div>

    <div class="card-right" style="width: 40%">
        <div style="border-style: solid; border-color:blue">
            <h2>Overzicht van je bestelling</h2>
            <div style="border-style: solid; border-color:red">
                <h3>Totaal: € <?php print($totalPrice); ?></h3>
            </div>
        </div>
    </div>
</div>