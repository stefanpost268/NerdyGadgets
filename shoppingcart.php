<?php
session_start();
include 'header.php';

// Check if the "Clear Session" button was clicked
if (isset($_POST['clear_session'])) {
    // Clear the session
    session_unset();
}

?>

<h1>This is product shopping cart list (DEV)</h1>
<table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($_SESSION["shoppingcart"])) {
            foreach ($_SESSION["shoppingcart"] as $productID => $productAmount) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($productID) . "</td>";
                echo "<td>" . htmlspecialchars($productAmount) . "</td>";
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
