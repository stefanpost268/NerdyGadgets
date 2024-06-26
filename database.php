<?php

function connectToDatabase() {
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect($_ENV["DATABASE_URL"], $_ENV["DATABASE_USER"], $_ENV["DATABASE_PASSWORD"], $_ENV["DATABASE_NAME"]);
        mysqli_set_charset($Connection, 'latin1');
        $DatabaseAvailable = true;
    } catch (mysqli_sql_exception $e) {
        $DatabaseAvailable = false;
    }
    if (!$DatabaseAvailable) {
        ?><h2>Website wordt op dit moment onderhouden.</h2><?php
        die();
    }

    return $Connection;
}


function getStockGroups($databaseConnection) {
    $Query = "
            SELECT StockGroupID, StockGroupName, ImagePath
            FROM stockgroups 
            WHERE StockGroupID IN (
                                    SELECT StockGroupID 
                                    FROM stockitemstockgroups
                                    ) AND ImagePath IS NOT NULL
            ORDER BY StockGroupID ASC";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $StockGroups = mysqli_fetch_all($Result, MYSQLI_ASSOC);
    return $StockGroups;
}

function getStockItem($id, $databaseConnection) {
    $Result = null;

    $Query = " 
           SELECT SI.StockItemID, 
            (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, 
            StockItemName,
            QuantityOnHand,
            SearchDetails, 
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            WHERE SI.stockitemid = ?
            GROUP BY StockItemID";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult && mysqli_num_rows($ReturnableResult) == 1) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC)[0];
    }

    return $Result;
}

/**
 * Return the images of a stock item or return group image.
 * 
 * @param int $id The id of the stock item
 * @param mysqli $databaseConnection The database connection
 * @return array The images of the stock item
 */
function getStockItemImage($id, $databaseConnection, $backupImagePath) {

    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $r = mysqli_stmt_get_result($Statement);
    $r = mysqli_fetch_all($r, MYSQLI_ASSOC);

    if(!empty($r)) {
        foreach ($r as $key => $value) {
            $r[$key]["ImagePath"] = "Public/StockItemIMG/".$value["ImagePath"];
        }
        return $r;
    } else {
        return array(
            array(
                "ImagePath" => "Public/StockGroupIMG/".$backupImagePath
            )
        );
    }

    return $r;
}

function getProductsOnPage($options) {
    if (isset($_GET['products_on_page'])) {
        $config = json_decode(file_get_contents(__DIR__ . "/Config/main.json"));
        $validOption = in_array($_GET['products_on_page'], $config->productsOnPageOptions);
        
        $productsOnPage = $validOption ? $_GET['products_on_page'] : $config->productsOnPageOptions[0];
        $_SESSION['products_on_page'] = $productsOnPage;
    } else if (isset($_SESSION['products_on_page'])) {
        $productsOnPage = $_SESSION['products_on_page'];
    } else {
        $productsOnPage = 25;
        $_SESSION['products_on_page'] = 25;
    }

    return $productsOnPage;
}

function getProducts($databaseConnection, $categoryID, $queryBuildResult, $search, $Sort, $orderBy, $ProductsOnPage, $offset) {
    $whereClause = empty($categoryID) ? "1=1" : $queryBuildResult . " ? IN (SELECT StockGroupID from stockitemstockgroups WHERE StockItemID = SI.StockItemID)";
    $searchQuery = (empty($search) ? "" : "AND SI.StockItemName LIKE '%" . $search . "%'");

    $query = "
        SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice,
        ROUND(SI.TaxRate * SI.RecommendedRetailPrice / 100 + SI.RecommendedRetailPrice, 2) as SellPrice,
        QuantityOnHand,
        (SELECT ImagePath FROM stockitemimages WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath,
        (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath
        FROM stockitems SI
        JOIN stockitemholdings SIH USING(stockitemid)
        JOIN stockitemstockgroups USING(StockItemID)
        JOIN stockgroups ON stockitemstockgroups.StockGroupID = stockgroups.StockGroupID
        WHERE {$whereClause}
    ";

    if (!empty($search)) {
        $search = '%' . $search . '%';
        $query .= " AND SI.StockItemName LIKE ?";
    }

    $query .= " GROUP BY StockItemID
        ORDER BY " . $Sort . " " . $orderBy . "
        LIMIT ? OFFSET ?
    ";

    $statement = mysqli_prepare($databaseConnection, $query);
    if (!empty($categoryID)) {
        if (!empty($search)) {
            mysqli_stmt_bind_param($statement, "isii", $categoryID, $search, $ProductsOnPage, $offset);
        } else {
            mysqli_stmt_bind_param($statement, "iii", $categoryID, $ProductsOnPage, $offset);
        }
    } else {
        if (!empty($search)) {
            mysqli_stmt_bind_param($statement, "sii", $search, $ProductsOnPage, $offset);
        } else {
            mysqli_stmt_bind_param($statement, "ii", $ProductsOnPage, $offset);
        }
    }
    mysqli_stmt_execute($statement);
    $returnableResult = mysqli_stmt_get_result($statement);
    $returnableResult = mysqli_fetch_all($returnableResult, MYSQLI_ASSOC);

    if(!empty($returnableResult)) {
        $countQuery = "
            SELECT count(*)
            FROM stockitems SI
            WHERE {$whereClause}
        ";

        $countQuery = $countQuery.(empty($search) ? "" : " AND SI.StockItemName LIKE '%" . $search . "%' OR SI.StockItemId = '$search'");

        $countStatement = mysqli_prepare($databaseConnection, $countQuery);
        if (!empty($categoryID)) {
            mysqli_stmt_bind_param($countStatement, "i", $categoryID);
        }

        mysqli_stmt_execute($countStatement);
        $countResult = mysqli_stmt_get_result($countStatement);
        $countResult = mysqli_fetch_all($countResult, MYSQLI_ASSOC);

        $count = $countResult[0]['count(*)'];
    } else {
        $count = 0;
    }

    return [
        'data' => $returnableResult,
        'count' => $count
    ];
}

function getStockImage($id, $databaseConnection, $item, $backupImagePath): string
{
    return getStockItemImage($id, $databaseConnection, $backupImagePath)[0]["ImagePath"];
}

function getShoppingCartItems($databaseConnection): array {
    $products = [];
    foreach ($_SESSION["shoppingcart"] as $id => $amount) {
        $item = getStockItem($id, $databaseConnection);
        $imagePath = getStockImage($id, $databaseConnection, $item, $item["BackupImagePath"]);
        $subtotal = round($amount * $item['SellPrice'], 2);

        $products[] = [
            "item" => $item,
            "image" => $imagePath,
            "amount" => $amount,
            'subtotal' => $subtotal,
        ];
    }

    return $products;
}

function getShippingCost($totalPrice): float {
    $config = json_decode(file_get_contents("Config/main.json"), true);
    if ($totalPrice > $config["freeShippingThreshold"]) {
        return 0;
    } else {
        return $config["shippingCost"];
    }
}

function getTotalPriceShoppingCart($products): float {
    $totalPrice = 0;
    foreach ($products as $product) {
        $totalPrice += $product['subtotal'];
    }

    return $totalPrice;
}

function getVoorraadTekst($actueleVoorraad) {
    if ($actueleVoorraad > 1000) {
        return "Ruime voorraad beschikbaar.";
    } else {
        return "Voorraad: $actueleVoorraad";
    }
}

function loadenv(string $envFile = '.env') {
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2) + [NULL, NULL];
            if ($key !== NULL && $value !== NULL) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    } else {
        throw new Exception('.env file not found');
    }
}

/**
 * Return a list of random products
 */
function getRandomProducts($databaseConnection) {
    $Result = null;

    $Query = " 
            SELECT SI.StockItemID, 
            (RecommendedRetailPrice*(1+(TaxRate/100))) AS SellPrice, 
            StockItemName,
            QuantityOnHand,
            SearchDetails, 
            (CASE WHEN (RecommendedRetailPrice*(1+(TaxRate/100))) > 50 THEN 0 ELSE 6.95 END) AS SendCosts, MarketingComments, CustomFields, SI.Video,
            (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath   
            FROM stockitems SI 
            JOIN stockitemholdings SIH USING(stockitemid)
            JOIN stockitemstockgroups ON SI.StockItemID = stockitemstockgroups.StockItemID
            JOIN stockgroups USING(StockGroupID)
            GROUP BY StockItemID
            ORDER BY RAND()
            LIMIT 10";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_execute($Statement);
    $ReturnableResult = mysqli_stmt_get_result($Statement);
    if ($ReturnableResult) {
        $Result = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);
    }

    return $Result;
}
