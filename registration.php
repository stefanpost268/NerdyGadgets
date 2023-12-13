<?php
session_start();
include "database.php";
loadenv();

$databaseConnection = connectToDatabase();

$register = registerAccount($databaseConnection);

$databaseConnection->close();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com%22%3E/">
    </script>
</head>

<body>

    <a onclick="history.back()" class="cursor">
        <svg fill="#000000" width="50px" height="50px" viewBox="0 0 52 52" data-name="Layer 1" id="Layer_1"
            xmlns="http://www.w3.org/2000/svg">
            <path
                d="M50,24H6.83L27.41,3.41a2,2,0,0,0,0-2.82,2,2,0,0,0-2.82,0l-24,24a1.79,1.79,0,0,0-.25.31A1.19,1.19,0,0,0,.25,25c0,.07-.07.13-.1.2l-.06.2a.84.84,0,0,0,0,.17,2,2,0,0,0,0,.78.84.84,0,0,0,0,.17l.06.2c0,.07.07.13.1.2a1.19,1.19,0,0,0,.09.15,1.79,1.79,0,0,0,.25.31l24,24a2,2,0,1,0,2.82-2.82L6.83,28H50a2,2,0,0,0,0-4Z" />
        </svg>
    </a>

    <img src="Public/ProductIMGHighRes/NerdyGadgetsLogo.png" alt="logo" class="center1">
    <form method="post">
        <div class="center">
            <h1 class="h1">Registreer je account.</h1>
            <div class="row">
                <label for="name">Naam:</label>
                <input type="text" class="form-control" name="name" id="name" maxlength="50" required>
                <label for="e-mail">E-mail:</label>
                <input type="email" class="form-control" name="e-mail" id="e-mail" required>
                <label for="password">Wachtwoord:</label>
                <input type="password" class="form-control" name="password" id="password"
                    pattern="((?=.*\d)(?=.*[A-Z])(?=.*\W)\w.{6,}\w)"
                    title="Minimum of 8 characters. Should have at least one special character, one number and a capital letter"
                    required>
                <label for="confirm-password">Confirmatie wachtwoord:</label>
                <input type="password" class="form-control" name="confirm-password" id="confirm-password" required>

                <div class="pt-5">
                    <input type="checkbox" name="checkbox" id="checkbox" required>
                    <label for="checkbox">Ik ga akkoord met de servicevoorwaarden.</label>
                </div>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <span class="font-medium">
                        <?= $register ?>
                    </span>
                </div>
                <div>
                    <button type="submit" class="centerbutton" onclick=>Registreer account</button>
                </div>
            </div>
        </div>
    </form>

    <style>
        .center {
            border: 3px solid green;
            text-align: center;
            height: 500px;
            width: 454px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 1.5%;
            padding: 30px;
        }

        .center1 {
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: auto;
            width: 10%;
        }

        .cursor {
            margin-left: 2%;
            margin-top: 40px;
            cursor: pointer;
            display: inline-block;
        }

        .h1 {
            font-size: 30px;
        }

        .centerbutton {
            margin-top: 60px;
            margin-left: 130px;
        }
    </style>
</body>

</html>