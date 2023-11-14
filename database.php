<!-- dit bestand bevat alle code die verbinding maakt met de database -->
<?php

function connectToDatabase() {
    $Connection = null;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
    try {
        $Connection = mysqli_connect("localhost", "root", "", "nerdygadgets");
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

function getHeaderStockGroups($databaseConnection) {
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
    $HeaderStockGroups = mysqli_stmt_get_result($Statement);
    return $HeaderStockGroups;
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
            CONCAT('Voorraad: ',QuantityOnHand)AS QuantityOnHand,
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

function getStockItemImage($id, $databaseConnection) {

    $Query = "
                SELECT ImagePath
                FROM stockitemimages 
                WHERE StockItemID = ?";

    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, "i", $id);
    mysqli_stmt_execute($Statement);
    $R = mysqli_stmt_get_result($Statement);
    $R = mysqli_fetch_all($R, MYSQLI_ASSOC);

    return $R;
}

function getProductsOnPage() {
    if (isset($_GET['products_on_page'])) {
        $ProductsOnPage = $_GET['products_on_page'];
        $_SESSION['products_on_page'] = $_GET['products_on_page'];
    } else if (isset($_SESSION['products_on_page'])) {
        $ProductsOnPage = $_SESSION['products_on_page'];
    } else {
        $ProductsOnPage = 25;
        $_SESSION['products_on_page'] = 25;
    }

    return $ProductsOnPage;
}

function getProducts($databaseConnection, $categoryID, $queryBuildResult, $search, $Sort, $orderBy, $ProductsOnPage, $offset) {
    $whereClause = empty($categoryID) ? "1=1" : $queryBuildResult . " ? IN (SELECT StockGroupID from stockitemstockgroups WHERE StockItemID = SI.StockItemID)";

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
        " . (empty($search) ? "" : "AND SI.StockItemName LIKE '%" . $search . "%'") . "
        GROUP BY StockItemID
        ORDER BY " . $Sort . " " . $orderBy . "
        LIMIT ? OFFSET ?
    ";

    $statement = mysqli_prepare($databaseConnection, $query);
    if (!empty($categoryID)) {
        mysqli_stmt_bind_param($statement, "iii", $categoryID, $ProductsOnPage, $offset);
    } else {
        mysqli_stmt_bind_param($statement, "ii", $ProductsOnPage, $offset);
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

        $countQuery = $countQuery.(empty($search) ? "" : " AND SI.StockItemName LIKE '%" . $search . "%'");

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