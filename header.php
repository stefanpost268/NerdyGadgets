<!-- de inhoud van dit bestand wordt bovenaan elke pagina geplaatst -->
<?php
session_start();
include "database.php";
loadenv();

$databaseConnection = connectToDatabase();

$HeaderStockGroups = getHeaderStockGroups($databaseConnection);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>NerdyGadgets</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- Javascript -->
    <script src="Public/JS/fontawesome.js"></script>
    <script src="Public/JS/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    
    <?php
        // get name of file
        $filename = basename($_SERVER['PHP_SELF']);
    ?>

    <!-- Style sheets-->
    <?php if(!in_array($filename, ["categories.php", "shoppingcart.php", "index.php", "checkout.php"])) { ?>
    <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css">
    <?php } ?>

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="bg-gray-700">
<div class="bg-gray-800">
    <div class="flex items-center justify-between p-4">
        <a href="./">
            <img src="Public/ProductIMGHighRes/NerdyGadgetsLogo.png" alt="NerdyGadgetsLogo" class="w-16 md:w-24">
        </a>
        <div class="flex gap-1">
            <?php foreach($HeaderStockGroups as $HeaderStockGroup) { ?>
                <a class="text-white hover:text-gray-300 flex hidden lg:block" href="browse.php?category_id=<?php print($HeaderStockGroup['StockGroupID']); ?>">
                    <?php print($HeaderStockGroup["StockGroupName"]); ?>
                </a>
            <?php } ?>

            <a href="categories.php" class="text-white hover:text-gray-300 flex hidden lg:block">
                Alle categorieën
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <a href="browse.php" class="text-white hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#ffffff">
                    <path d="M23.111 20.058l-4.977-4.977c.965-1.52 1.523-3.322 1.523-5.251 0-5.42-4.409-9.83-9.829-9.83-5.42 0-9.828 4.41-9.828 9.83s4.408 9.83 9.829 9.83c1.834 0 3.552-.505 5.022-1.383l5.021 5.021c2.144 2.141 5.384-1.096 3.239-3.24zm-20.064-10.228c0-3.739 3.043-6.782 6.782-6.782s6.782 3.042 6.782 6.782-3.043 6.782-6.782 6.782-6.782-3.043-6.782-6.782zm2.01-1.764c1.984-4.599 8.664-4.066 9.922.749-2.534-2.974-6.993-3.294-9.922-.749z"/>
                </svg>
            </a>
            <a href="shoppingcart.php" class="text-white hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="#ffffff">
                    <path d="M24 3l-.743 2h-1.929l-3.474 12h-13.239l-4.615-11h16.812l-.564 2h-13.24l2.937 7h10.428l3.432-12h4.195zm-15.5 15c-.828 0-1.5.672-1.5 1.5 0 .829.672 1.5 1.5 1.5s1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5zm6.9-7-1.9 7c-.828 0-1.5.671-1.5 1.5s.672 1.5 1.5 1.5 1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5z"/>
                </svg>
                <?php
                    if (isset($_SESSION["shoppingcart"]) && !empty($_SESSION["shoppingcart"])) {
                        print('<div class="bg-red-500 text-white px-2 py-1 rounded-full">' . count($_SESSION["shoppingcart"]) . '</div>');
                    }
                ?>
            </a>
            <a href="categories.php" class="text-white hover:text-gray-300 block lg:hidden">
                <img src="./Public/SVG/category.svg" alt="Categories" width="30" height="30">
                
            </a>
        </div>
    </div>
</div>