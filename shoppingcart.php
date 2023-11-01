<?php
session_start();
include 'header.php';

// Check if the "Clear Session" button was clicked
if (isset($_POST['clear_session'])) {
    // Clear the session
    session_unset();
}

$products = [];
if(isset($_SESSION["shoppingcart"])) {
    foreach($_SESSION["shoppingcart"] as $id => $amount) {
        $products[] = getStockItem($id, $databaseConnection);
    }
}

?>

<h1>This is product shopping cart list (DEV)</h1>
<table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Amount</th>
            <th>Product Name</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($products)) {
            foreach ($products as $product) {
                echo "<tr>";
                echo "<td>" . ($product["StockItemID"]) . "</td>";
                echo "<td>" . round($product["SellPrice"]) . "</td>";
                echo "<td>" . ($product["StockItemName"]) . "</td>";
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
