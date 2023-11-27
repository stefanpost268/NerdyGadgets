<!-- This file contains all the code for the page that displays a single product -->
<?php
    include __DIR__ . "/header.php";

    // Add to shopping bag
    if (isset($_POST["articleid"]) && isset($_POST["amount"])){
        $amount = intval($_POST["amount"]);

        if(!isset($_SESSION["shoppingcart"])) {
            $_SESSION["shoppingcart"] = array();
        }

        $id = $_POST["articleid"];
        if(isset($_SESSION["shoppingcart"][$id])) {
            if($amount < 1) {
                unset($_SESSION["shoppingcart"][$id]);
                $status = "deleted";
                $succesfull = true;
            } else {
                $status = "updated";
                $succesfull = $_SESSION["shoppingcart"][$id] = $amount;
            }
        } else {
            if($amount >= 1) {
                $status = "added";
                $succesfull = $_SESSION["shoppingcart"][$id] = $amount;
            }
        }
    } else {
        $id = $_GET["id"] ?? NULL;
    }

    // Get current shopping bag amount of product.
    if(isset($_SESSION["shoppingcart"][$id])) {
        $amount = $_SESSION["shoppingcart"][$id];
    } else {
        $amount = 0;
    }

    $StockItem = getStockItem($id, $databaseConnection);
    $StockItemImage = getStockItemImage($id, $databaseConnection);
?>

<div id="CenteredContent">
    <?php if(isset($succesfull)) { ?>
        <?php if($succesfull) { ?>
            <div class="alert alert-success" role="alert">
                <?php
                    switch($status) {
                        case "added": echo "Uw product is toegevoegd aan de winkelmand!"; break;
                        case "updated": echo "Uw product aantal is in de winkelmand aangepast!"; break;
                        case "deleted": echo "Uw product is uit de winkelmand verwijderd!"; break;
                    }
                ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-danger" role="alert">
                Helaas, we kunnen dit product niet in uw winkelmand zetten!
            </div>
        <?php } ?>
    <?php } ?>
    <?php
    if ($StockItem != null) {
    ?>
    <?php
    if (isset($StockItem['Video'])) {
    ?>
    <div id="VideoFrame">
        <?php echo $StockItem['Video']; ?>
    </div>
    <?php
    }
    ?>

    <div id="ArticleHeader">
        <?php
        if (!empty($StockItemImage)) {
            // Show a single image
            if (count($StockItemImage) == 1) {
        ?>
        <div id="ImageFrame" style="background-image: url('Public/StockItemIMG/<?php echo $StockItemImage[0]['ImagePath']; ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
        <?php
            } elseif (count($StockItemImage) >= 2) {
        ?>
        <!-- Show a carousel of multiple images -->
        <div id="ImageFrame">
            <div id="ImageCarousel" class="carousel slide" data-interval="false">
                <!-- Indicators -->
                <ul class="carousel-indicators">
                    <?php for ($i = 0; $i < count($StockItemImage); $i++) { ?>
                    <li data-target="#ImageCarousel" data-slide-to="<?php echo $i ?>" <?php echo ($i == 0) ? 'class="active"' : ''; ?>></li>
                    <?php } ?>
                </ul>

                <!-- Slideshow -->
                <div class="carousel-inner">
                    <?php for ($i = 0; $i < count($StockItemImage); $i++) { ?>
                    <div
                        class="carousel-item <?php echo ($i == 0) ? 'active' : ''; ?>"
                    >
                        <img src="Public/StockItemIMG/<?php echo $StockItemImage[$i]['ImagePath']; ?>">
                    </div>
                    <?php } ?>
                </div>

                <!-- Previous and Next buttons -->
                <a class="carousel-control-prev" href="#ImageCarousel" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </a>
                <a class="carousel-control-next" href="#ImageCarousel" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </a>
            </div>
        </div>
        <?php
            }
        } else {
        ?>
        <div id="ImageFrame" style="background-image: url('Public/StockGroupIMG/<?php echo $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
        <?php
        }
        ?>

        <h1 class="StockItemID">Artikelnummer: <?php echo $StockItem["StockItemID"]; ?></h1>
        <h2 class="StockItemNameViewSize StockItemName">
            <?php echo $StockItem['StockItemName']; ?>
        </h2>
        <div class="QuantityText">Voorraad: <?php echo $StockItem['QuantityOnHand']; ?></div>
        <div id="StockItemHeaderLeft">
            <div class="CenterPriceLeft">
                <div class="CenterPriceLeftChild">
                    <p class="StockItemPriceText"><b><?php echo sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></p>
                    <h6> Inclusief BTW </h6>
                </div>
            </div>
        </div>
    </div>

    <div id="StockItemDescription">
        <h3>Artikel beschrijving</h3>
        <p><?php echo $StockItem['SearchDetails']; ?></p>
    </div>

    <div id="StockItemSpecifications">
        <h3>Artikel specificaties</h3>
        <?php
        $CustomFields = json_decode($StockItem['CustomFields'], true);
        if (is_array($CustomFields)) {
        ?>
        <table>
            <thead>
                <th>Naam</th>
                <th>Data</th>
            </thead>
            <?php
            foreach ($CustomFields as $SpecName => $SpecText) {
            ?>
            <tr>
                <td><?php echo $SpecName; ?></td>
                <td>
                    <?php
                    if (is_array($SpecText)) {
                        foreach ($SpecText as $SubText) {
                            echo $SubText . " ";
                        }
                    } else {
                        echo $SpecText;
                    }
                    ?>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
        <?php
        } else {
        ?>
        <p><?php echo $StockItem['CustomFields']; ?>.</p>
        <?php
        }
        ?>
    </div>

    <div id="addToShoppingCart">
        <form method="post" action="productpage.php"> 
        <input type="hidden" name="articleid" value="<?php print($id);?>"> 
        <input type="number" id="amount" name="amount" value="<?php print($amount); ?>" min="0" max="100">
        <br><br> 
        <input type="submit" value="In Winkelwagen"> 
        </form> 
    </div>
        
    <?php
    } else {
    ?>
        <h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2>
    <?php
    }
    ?>
</div>
