<?php
session_start();
include "database.php";
loadenv();

$databaseConnection = connectToDatabase();

if(isset($_POST["wachtwoord"])) {
    var_dump($_POST);
}
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


    <!-- Javascript -->
    <!-- <script src="Public/JS/fontawesome.js"></script>
    <script src="Public/JS/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script> -->

    <!-- Style sheets-->
    <!-- <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css"> -->


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
        <h1 class="h2">Registreer je account.</h1>
        <div class="row">
            <label for="naam">Naam:</label>
            <input type="text" class="form-control" name="naam" id="naam" required>
            <label for="e-mail">E-mail:</label>
            <input type="email" class="form-control" name="e-mail" id="e-mail" required>
            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" class="form-control" name="wachtwoord" id="wachtwoord" pattern="((?=.*\d)(?=.*[A-Z])(?=.*\W)\w.{6,18}\w)" title="Minimum of 8 characters. Should have at least one special character, one number and a capital letter    " required>
            <label for="cwachtwoord">Confirmatie wachtwoord:</label>
            <input type="password" class="form-control" name="cwachtwoord" id="cwachtwoord" required>

            <div class="pt-5">
                <input type="checkbox" name="checkbox" id="checkbox" required>
                <label for="checkbox">Ik ga akkoord met de servicevoorwaarden.</label>
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
            margin-top: 40px !important;
            cursor: pointer;
            display: inline-block;
        }

        .h2 {
            font-size: 30px;
        }

        .centerbutton {
            margin-top: 60px;
            margin-left: 130px;
        }
    </style>
</body>

</html>