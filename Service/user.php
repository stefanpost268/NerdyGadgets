<?php

declare(strict_types=1);

namespace Service;

use mysqli;

class User
{
    /**
     * Update or create user in database.
     * 
     * @param mysqli $databaseConnection
     * @param array $formData
     * @return int returns id of user.
     */
    public function updateOrCreateUser(mysqli $databaseConnection, array $userData): int
    {
        $query = "INSERT INTO `User` (`email`,`name`) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE 
            `email` = VALUES(`email`),
            `name` = VALUES(`name`),
            `id` = LAST_INSERT_ID(`id`);
        ";

        $statement = mysqli_prepare($databaseConnection, $query);

        mysqli_stmt_bind_param($statement, 'ss', $userData['email'], $userData['name']);

        $success = mysqli_stmt_execute($statement);
        $insertedOrUpdatedId = ($success) ? mysqli_insert_id($databaseConnection) : 0;
        mysqli_stmt_close($statement);

        return $insertedOrUpdatedId;
    }
}