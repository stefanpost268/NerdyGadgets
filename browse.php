<!-- dit bestand bevat alle code voor het productoverzicht -->
<?php
include __DIR__ . "/header.php";

function getVoorraadTekst($actueleVoorraad) {
    if ($actueleVoorraad > 1000) {
        return "Ruime voorraad beschikbaar.";
    } else {
        return "Voorraad: $actueleVoorraad";
    }
}

function berekenVerkoopPrijs($adviesPrijs, $btw) {
    return $btw * $adviesPrijs / 100 + $adviesPrijs;
}

$returnableResult = null;

$amountOfPages = 0;
$queryBuildResult = "";

$ProductsOnPage = getProductsOnPage();
$categoryID = isset($_GET['category_id']) ? $_GET['category_id'] : "";
$pageNumber = isset($_GET['page_number']) ? $_GET['page_number'] : 0;
$orderByLabel = $_GET['order_by'] ?? "name-ASC";
$search = $_GET['search'] ?? "";
$offset = $pageNumber * $ProductsOnPage;

switch($orderByLabel) {
    case "price-ASC":
        $orderBy = "ASC";
        $Sort = "SellPrice";
        break;
    case "price-DESC":
        $orderBy = "DESC";
        $Sort = "SellPrice";
        break;
    case "name-DESC":
        $orderBy = "DESC";
        $Sort = "StockItemName";
        break;
    default:
        $orderBy = "ASC";
        $Sort = "StockItemName";
}


if ($categoryID != "") { 
    if ($queryBuildResult != "") {
    $queryBuildResult .= " AND ";
    }
}

if ($categoryID !== "") {
    $Query = "
           SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice,
           ROUND(SI.TaxRate * SI.RecommendedRetailPrice / 100 + SI.RecommendedRetailPrice,2) as SellPrice,
           QuantityOnHand,
           (SELECT ImagePath FROM stockitemimages WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
           (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
           FROM stockitems SI
           JOIN stockitemholdings SIH USING(stockitemid)
           JOIN stockitemstockgroups USING(StockItemID)
           JOIN stockgroups ON stockitemstockgroups.StockGroupID = stockgroups.StockGroupID
           WHERE " . $queryBuildResult . " ? IN (SELECT StockGroupID from stockitemstockgroups WHERE StockItemID = SI.StockItemID)
           " . (empty($search) ? "" : "AND SI.StockItemName LIKE '%".$search."%'") . "
           GROUP BY StockItemID
           ORDER BY " . $Sort . " " . $orderBy . "
           LIMIT ? OFFSET ?
    ";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "iii", $categoryID, $ProductsOnPage, $offset);
    mysqli_stmt_execute($Statement);
    $returnableResult = mysqli_stmt_get_result($Statement);
    $returnableResult = mysqli_fetch_all($returnableResult, MYSQLI_ASSOC);

    $Query = "
                SELECT count(*)
                FROM stockitems SI
                WHERE " . $queryBuildResult . " ? IN (SELECT SS.StockGroupID from stockitemstockgroups SS WHERE SS.StockItemID = SI.StockItemID)";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $categoryID);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);

}

$amount = $Result[0] ?? null;

if (isset($amount)) {
    $amountOfPages = ceil($amount["count(*)"] / $ProductsOnPage);
}
?>

<div class="d-flex">
    <form id="sideBar">
        <p id="sideBarTitle">Filteren</p>
        <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($_GET['category_id'])) {
            print ($_GET['category_id']);
        } ?>">

        <div class="m-1">
            <label for="search">Zoeken:</label>
            <input type="text" name="search" id="search" value="<?php if (isset($_GET['search'])) {
                print ($_GET['search']);
            } ?>">

        <label class="pt-3" for="order_by">Sorteer op:</label>
        <select name="order_by" id="order_by" onchange="this.form.submit()">
            <option value="price-ASC" <?php print($orderByLabel == "price-ASC" ? "selected" : ""); ?> >Prijs oplopend</option>
            <option value="price-DESC" <?php print($orderByLabel == "price-DESC" ? "selected" : ""); ?> >Prijs aflopend</option>
            <option value="name-ASC" <?php print($orderByLabel== "name-ASC" ? "selected" : ""); ?> >Naam oplopend</option>
            <option value="name-DESC" <?php print($orderByLabel == "name-DESC" ? "selected" : ""); ?> >Naam aflopend</option>
        </select>

        <label class="pt-3" for="products_on_page">Selecteer het aantal producten:</label>
        <select name="products_on_page" onchange="this.form.submit()">
            <option value="25" <?php print($ProductsOnPage == 25 ? "selected" : "") ?> >25</option>
            <option value="50" <?php print($ProductsOnPage == 50 ? "selected" : "") ?>>50</option>
            <option value="100" <?php print($ProductsOnPage == 100 ? "selected" : "") ?>>100</option>
        </select>
    </form>
</div>


<!-- einde zoekresultaten die links van de zoekbalk staan -->
<!-- einde code deel 3 van User story: Zoeken producten  -->
<div id="ResultsArea" class="Browse">
    
        <?php
        if (isset($returnableResult) && count($returnableResult) > 0) {
            foreach ($returnableResult as $row) {
                ?>
                    <div id="ProductFrame">
                        <?php
                        if (isset($row['ImagePath'])) { ?>
                            <div class="ImgFrame"
                                style="background-image: url('<?php print "Public/StockItemIMG/" . $row['ImagePath']; ?>'); background-size: 230px; background-repeat: no-repeat; background-position: center;"></div>
                        <?php } else if (isset($row['BackupImagePath'])) { ?>
                            <div class="ImgFrame"
                                style="background-image: url('<?php print "Public/StockGroupIMG/" . $row['BackupImagePath'] ?>'); background-size: cover;"></div>
                        <?php }
                        ?>

                        <div id="StockItemFrameRight">
                            <div class="CenterPriceLeftChild">
                                <h1 class="StockItemPriceText"><?php print sprintf(" %0.2f", berekenVerkoopPrijs($row["RecommendedRetailPrice"], $row["TaxRate"])); ?></h1>
                                <h6>Inclusief BTW </h6>
                            </div>
                        </div>
                        <h1 class="StockItemID">Artikelnummer: <?php print $row["StockItemID"]; ?></h1>
                        <a href="productpage.php?id=<?php echo $row['StockItemID']; ?>" class="StockItemName"><?php echo $row["StockItemName"]; ?></a>
                        <p class="StockItemComments"><?php print $row["MarketingComments"]; ?></p>
                        <h4 class="ItemQuantity"><?php print getVoorraadTekst($row["QuantityOnHand"]); ?></h4>
                    </div>
            <?php } ?>

            <form id="PageSelector">
                <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($_GET['category_id'])) {
                    print ($_GET['category_id']);
                } ?>">
                <input type="hidden" name="result_page_numbers" id="result_page_numbers"
                    value="<?php print (isset($_GET['result_page_numbers'])) ? $_GET['result_page_numbers'] : "0"; ?>">
                <input type="hidden" name="products_on_page" id="products_on_page"
                    value="<?php print ($_SESSION['products_on_page']); ?>">

                <?php
                if ($amountOfPages > 0) {
                    for ($i = 1; $i <= $amountOfPages; $i++) {
                        if ($pageNumber == ($i - 1)) {
                            ?>
                            <div id="SelectedPage"><?php print $i; ?></div><?php
                        } else { ?>
                            <button id="page_number" class="PageNumber" value="<?php print($i - 1); ?>" type="submit"
                                    name="page_number"><?php print($i); ?></button>
                        <?php }
                    }
                }
                ?>
            </form>
            <?php
        } else {
            ?>
            <h2 id="NoSearchResults">
                Helaas, uw zoek opdracht voor
                <u><?php print (isset($_GET['search'])) ? $_GET['search'] : ""; ?></u>
                heeft geen resultaten opgeleverd.
            </h2>
            <?php
        }
        ?>
    </div>
</body>
</html>

<?php
include __DIR__ . "/footer.php";
?>

<style>
    #ResultsArea {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    #ResultsArea::-webkit-scrollbar {
        display: none;
    }
</style>
