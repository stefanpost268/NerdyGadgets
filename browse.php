<!-- dit bestand bevat alle code voor het productoverzicht -->
<?php
include __DIR__ . "/header.php";

function berekenVerkoopPrijs($adviesPrijs, $btw)
{
    return $btw * $adviesPrijs / 100 + $adviesPrijs;
}

$returnableResult = null;
$queryBuildResult = "";

$productsOnPage = getProductsOnPage();

$categoryID = isset($_GET['category_id']) ? $_GET['category_id'] : NULL;
$pageNumber = isset($_GET['page_number']) ? $_GET['page_number'] : 0;
$orderByLabel = $_GET['order_by'] ?? "name-ASC";
$search = $_GET['search'] ?? "";
$offset = $pageNumber * $productsOnPage;

switch ($orderByLabel) {
    case "price-ASC":
        $orderBy = "ASC";
        $sort = "SellPrice";
        break;
    case "price-DESC":
        $orderBy = "DESC";
        $sort = "SellPrice";
        break;
    case "name-DESC":
        $orderBy = "DESC";
        $sort = "StockItemName";
        break;
    default:
        $orderBy = "ASC";
        $sort = "StockItemName";
}


if ($categoryID !== null) {
    if ($queryBuildResult != "") {
        $queryBuildResult .= " AND ";
    }
}

$Result = getProducts(
    $databaseConnection,
    $categoryID,
    $queryBuildResult,
    $search,
    $sort,
    $orderBy,
    $productsOnPage,
    $offset
);

$amount = $Result['count'] ?? null;
$amountOfPages = isset($amount) ? ceil($amount / $productsOnPage) : 0;
?>

<main class="min-h-screen flex flex-col md:flex-row mx-auto max-w-screen-lg">
    <div id="filterSidebar" class="hidden md:block md:w-1/3 bg-gray-800 p-4 md:ml-10 rounded-md md:mt-10 shadow-md m-1 md:sticky md:top-10 md:max-h-[calc(100vh-150px)] md:overflow-y-auto">
        <p id="sideBarTitle" class="text-lg text-white font-bold mb-4">Filteren</p>
        <?php include "./Components/filterFormData.php" ?>
    </div>

    <?php include "./Components/filterDropdown.php"; ?>

    <div id="ResultsArea" class="md:w-2/3 m-1 md:m-10">
        <?php if (isset($Result['data']) && $amount > 0) : ?>
            <?php foreach ($Result['data'] as $row) : ?>
                <div class="mb-4 p-6 bg-gray-800 text-white rounded-md shadow-md overflow-hidden w-full">
                    <?php if (isset($row['ImagePath'])) : ?>
                        <div class="md:w-48 md:h-48 h-28 w-28 bg-cover bg-center rounded-md float-left mr-4" style="background-image: url('<?= "Public/StockItemIMG/" . $row['ImagePath'] ?>');"></div>
                    <?php elseif (isset($row['BackupImagePath'])) : ?>
                        <div class="w-48 h-48 bg-cover bg-center rounded-md float-left mr-4" style="background-image: url('<?= "Public/StockGroupIMG/" . $row['BackupImagePath'] ?>');"></div>
                    <?php endif; ?>

                    <div class="hidden md:flex items-center mb-2">
                        <div class="ml-auto text-gray-300">
                            <h1 class="text-lg font-bold"><?= sprintf("€%0.2f", berekenVerkoopPrijs($row['RecommendedRetailPrice'], $row['TaxRate'])) ?></h1>
                            <h6 class="text-sm">Inclusief BTW</h6>
                        </div>
                    </div>
                    <h1 class="text-gray-400 text-sm">Artikelnummer: <?= $row['StockItemID'] ?></h1>
                    <a href="productpage.php?id=<?= $row['StockItemID'] ?>" class="text-blue-400 hover:underline md:text-lg font-semibold"><?= $row['StockItemName'] ?></a>
                    <p class="text-gray-400 text-sm"><?= $row['MarketingComments'] ?></p>
                    <div class="flex md:hidden text-gray-300">
                        <h1 class="text- font-bold"><?= sprintf("€%0.2f", berekenVerkoopPrijs($row['RecommendedRetailPrice'], $row['TaxRate'])) ?></h1>
                        <h6 class="text-xs ml-1">Inclusief BTW</h6>
                    </div>
                    <h4 class="text-green-400 text-sm md:text-lg"><?= getVoorraadTekst($row['QuantityOnHand']) ?></h4>
                </div>
            <?php endforeach; ?>
            <form id="PageSelector" class="mt-4 flex items-center justify-center">
                <input type="hidden" name="category_id" id="category_id" value="<?= isset($_GET['category_id']) ? $_GET['category_id'] : '' ?>">

                <?php if ($amountOfPages > 0) : ?>
                    <div class="flex space-x-2 pb-5">
                        <?php for ($i = 1; $i <= $amountOfPages; $i++) : ?>
                            <?php if ($pageNumber == ($i - 1)) : ?>
                                <div id="SelectedPage" class="bg-blue-500 text-white px-3 py-1 rounded-full"><?= $i ?></div>
                            <?php else : ?>
                                <button id="page_number" class="PageNumber bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded-full" value="<?= $i - 1 ?>" type="submit" name="page_number"><?= $i ?></button>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </form>
        <?php else : ?>
            <div class="flex flex-col items-center justify-center text-center bg-gray-800 p-8 rounded-lg shadow-md">
                <h1 class="text-white text-4xl font-bold mb-4">Geen producten gevonden</h1>
                <img class="bg-white rounded-xl shadow-md" src="./Public/SVG/not-found.svg" alt="Empty Cart" width="150" height="150">
                <p class="text-gray-300 mt-4">
                    Helaas, uw zoek opdracht voor
                    <u><?= isset($_GET['search']) ? $_GET['search'] : '' ?></u>
                    heeft geen resultaten opgeleverd.
                </p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include __DIR__ . "/footer.php";
?>