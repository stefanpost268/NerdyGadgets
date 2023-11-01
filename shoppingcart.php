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
    }
}
?>

<div style="padding: 25px;">
    <h1>Winkelwagen</h1>
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
                        <td><img width='100' src='<?php echo $product["image"]; ?>'></td>
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
