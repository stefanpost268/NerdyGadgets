<?php
session_start();
include 'header.php';

function getProductImage($id, $databaseConnection, $item): string
{
    $stockImage = getStockItemImage($id, $databaseConnection);

    if(isset($stockImage[0]["ImagePath"])) {
        return "Public/StockItemIMG/".getStockItemImage($id, $databaseConnection)[0]["ImagePath"];
    } else {
        return "Public/StockGroupIMG/".$item["BackupImagePath"];
    }
}


// Check if the "Clear Session" button was clicked
if (isset($_POST['clear_session'])) {
    // Clear the session
    session_unset();
}

$products = [];
if(isset($_SESSION["shoppingcart"])) {
    foreach($_SESSION["shoppingcart"] as $id => $amount) {
        $item = getStockItem($id, $databaseConnection);
        $products[] = array(
            "item" => $item,
            "image" => getProductImage($id, $databaseConnection, $item),
            "amount" => $amount
        );
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
            <?php
            if (!empty($products)) {
                foreach ($products as $product) {
                    echo "<tr>";
                    echo "<td><img width='100' src='".$product["image"]."'>";
                    echo "<td> €" . round($product["item"]["SellPrice"], 2) . "</td>";
                    echo "<td>".$product["amount"]."</td>";
                    echo "<td>" . ($product["item"]["StockItemName"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Shopping cart is empty</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <form method="post">
        <button type="submit" name="clear_session">Clear Session</button>
    </form>
</div>

