<?php
    include 'header.php';

    $products = [];
    $totalPrice = 0;

    if (!isset($_SESSION["shoppingcart"])) {
        header("Location: ./shoppingcart.php");
    }

    $products = getShoppingCartItems($databaseConnection);
    $totalPrice = getTotalPriceShoppingCart($products);
?>

<div style="width: 70%; margin: 0 auto; margin-top: 50px;">
    <div style="display: flex; justify-content: space-between; align-items: center">
        <div>
            <h1>Voltooi uw bestelling</h1>
            <h2>Vul uw gegevens in voordat u bestelt</h2>
        </div>
        <button class="btn btn-primary" onclick="window.location = './shoppingcart.php'">Back</button>
    </div>
    <hr style="text-align:left;margin-left:0;background-color: white;"> 
    <div style="display: flex; justify-content: space-between;">
        <div style="width: 45%;">
            <h3>Klant informatie</h3>
            <label for="naam">Naam</label>
            <input type="text" name="naam" id="naam">
            <h3 style="margin-top: 10px;">Aflever adres</h3>
            <label for="postcode">Postcode</label>
            <input type="text" name="postcode" id="postcode">
            <label for="huisnummer">Huisnummer</label>
            <input type="text" name="huisnummer" id="huisnummer">
            <label for="woonplaats">Woonplaats</label>
            <input type="text" name="woonplaats" id="woonplaats">
        </div>
        <div style="width: 50%">
            <h3>Je bestelling</h3>
            <div style="border: 2px solid white; padding: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center">
                    <p>Product</p>
                    <p>Subtotaal</p>
                </div>
                <?php foreach ($products as $product) { ?>
                    <div style="display:flex; justify-content: space-between; position: relative; margin-top: 10px;">
                        <div style="position: relative;">
                            <img width='75' src='<?php echo $product["image"]; ?>' class="img-thumbnail">
                            <div style="position: absolute; top: 0; right: 0; background-color: red; color: white; padding: 5px; border-radius: 50%;">
                                <?php echo $product["amount"]; ?>
                            </div>
                        </div>
                        <div style="flex: 1; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo $product["item"]["StockItemName"] ?>
                        </div>
                        <p>€<?php print(number_format(($product["item"]["SellPrice"] * $product["amount"]), 2)) ?></p>
                    </div>
                <?php } ?>
                <hr style="text-align:left;margin-left:0;background-color: white;">
                <div style="display: flex;">
                    <h3>Totaal: €<?php echo number_format($totalPrice, 2); ?></h3>
                    <p style="margin-left: 10px;">Inclusief BTW</p>
                </div>
                <button class="btn btn-primary" style="margin-left: auto; margin-right: 0; width: 100%">Afrekenen</button>
            </div>
        </div>
    </div>
</div>