<?php

declare(strict_types=1);

namespace Service;

use mysqli;

class product {
    /**
     * Bind products to transaction in database.
     * 
     * @param int $databaseId
     * @param array $shoppingCart
     * @param mysqli $databaseConnection
     * @return bool
     */
    public function bindProductsOnTransaction(int $databaseId, array $shoppingCart, mysqli $databaseConnection): bool
    {
        $query = "INSERT INTO `TransactionBind` (
            `transactionId`,
            `stockitemId`,
            `amount`
        ) VALUES (?,?,?);
        ";

        $statement = mysqli_prepare($databaseConnection, $query);

        foreach ($shoppingCart as $productId => $amount) {
            mysqli_stmt_bind_param($statement, 'iii', $databaseId, $productId, $amount);
            mysqli_stmt_execute($statement);
        }

        mysqli_stmt_close($statement);

        return true;
    }

    /**
     * Removes items from stock of a product.
     * 
     * @param int $productId
     * @param int $amount
     * @param mysqli $databaseConnection
     * @return bool
     */
    public function updateStockQuantityOnProduct(int $productId, int $amount, mysqli $databaseConnection): bool
    {
        $query = "UPDATE `stockitemholdings` SET `QuantityOnHand` = `QuantityOnHand` - ? WHERE `StockItemID` = ?;";

        $statement = mysqli_prepare($databaseConnection, $query);
        mysqli_stmt_bind_param($statement, 'ii', $amount, $productId);
        $success = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $success;
    }

    /**
     * Get all products from database.
     * 
     * @param mysqli $databaseConnection
     * @return array
     */
    function getHeaderStockGroups(mysqli $databaseConnection): array {
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
        
        $HeaderStockGroups = mysqli_fetch_all(mysqli_stmt_get_result($Statement), MYSQLI_ASSOC);
    
        mysqli_stmt_close($Statement);
    
        return $HeaderStockGroups;
    }
}