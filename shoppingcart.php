<?php
include 'header.php';

if(isset($_POST["productAmount"])) {
    if($_POST["productAmount"] != $_SESSION["shoppingcart"]) {
        $items = $_POST["productAmount"];
        foreach($items as $id => $amount) {
            if($amount > 100) { $amount = 100;}
            if($amount < 1) {
                unset($_SESSION["shoppingcart"][$id]);
            } else if(is_numeric($amount)) {
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

<div class="container">
    <h1 class="pb-5">Winkelwagen</h1>
    <div class="row">
        <div class="col-lg-7">
            <form method="POST">
                <div class="table-responsive"> <!-- Add this class for responsive tables -->
                    <table class="table" style="color:white;">
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
                                            <div class="d-flex align-items-center">
                                                <img width='100' src='<?php echo $product["image"]; ?>' class="img-thumbnail">
                                                <div class="ml-3 color:red;">
                                                    <p><a href="./productpage.php?id=<?php print($product["item"]["StockItemID"]); ?>"><?php print($product["item"]["StockItemName"]); ?></a></p>
                                                    <p><?php print("Article ID: " . $product["item"]["StockItemID"]); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>€<?php echo number_format($product["item"]["SellPrice"], 2); ?></td>
                                        <td>
                                            <input type="number" max="100" min="0" name="productAmount[<?php print($product["item"]["StockItemID"]); ?>]" value="<?php print($product['amount']) ?>" onchange="this.form.submit()" />
                                        </td>
                                        <td>€ <?php echo number_format($product["subtotal"], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan='4'>Winkelmand is leeg</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card" style="background-color: #23272e; border-style: solid; border-color: white;">
                <div class="card-body">
                    <h2>Overzicht van je bestelling</h2>
                    <div style="border-style: solid; border-color: white; padding: 5px;">
                        <h3>Totaal: € <?php print($totalPrice); ?></h3>
                        <button class="btn btn-primary" style="width: 100%;" type="submit">
                            <?php if (empty($products)) { ?>
                                <a style="color: white;" href="./browse.php">
                                    Ga naar de winkel
                                </a>
                            <?php } else { ?>
                                <a style="color: white;" href="./checkout.php">
                                    Bestelling plaatsen
                                </a>
                            <?php } ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

