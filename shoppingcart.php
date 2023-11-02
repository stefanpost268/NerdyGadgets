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

if(isset($_POST["productAmount"])) {
    if($_POST["productAmount"] != $_SESSION["shoppingcart"]) {
        $items = $_POST["productAmount"];
        foreach($items as $id => $amount) {
            if($amount == 0) {
                unset($_SESSION["shoppingcart"][$id]);
            } else if(is_numeric($amount)) {
                $_SESSION["shoppingcart"][$id] = round($amount);
            } else {
                $_SESSION["shoppingcart"][$id] = 0;
            }
        }
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

<div class="container">
<h1 class="pb-5">Winkelwagen</h1>
    <div class="row">
        <div class="col-lg-7">
            <form method="POST">
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
                                            <p><?php print($product["item"]["StockItemName"]); ?></p>
                                            <p><?php  print("Article ID: " . $product["item"]["StockItemID"]); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>€<?php echo number_format($product["item"]["SellPrice"], 2); ?></td>
                                <td>
                                    <input
                                        type="number"
                                        max="100"
                                        min="0"
                                        name="productAmount[<?php print($product["item"]["StockItemID"]); ?>]"
                                        value="<?php print($product['amount']) ?>"
                                        onchange="this.form.submit()"
                                    />
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
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card" style="background-color: #23272e; border-style: solid; border-color: white;">
                <div class="card-body">
                    <h2>Overzicht van je bestelling</h2>
                    <div style="border-style: solid; border-color: white; padding: 5px;">
                        <h3>Totaal: € <?php print($totalPrice); ?></h3>
                        <form method="post">
                            <button
                                class="btn btn-primary"
                                style="width: 100%;"
                                type="submit"
                                name="clear_session"
                            >
                                Legen Winkelmand
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
