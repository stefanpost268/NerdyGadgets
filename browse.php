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
<div class="md:w-1/3 bg-gray-800 text-white p-4 md:ml-10 rounded-md md:mt-10 shadow-md m-1 md:sticky md:top-10 md:max-h-[calc(100vh-150px)] md:overflow-y-auto">
        <form id="sideBar" class="">
            <p id="sideBarTitle" class="text-lg font-bold mb-4">Filteren</p>
            <input type="hidden" name="category_id" id="category_id" value="<?= isset($_GET['category_id']) ? $_GET['category_id'] : '' ?>">

            <div class="mt-2">
                <label for="search" class="block text-sm">Zoeken:</label>
                <input type="text" name="search" id="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" class="w-full mb-2 p-2 bg-gray-700 text-white rounded">

                <label class="block text-sm" for="order_by">Sorteer op:</label>
                <select name="order_by" id="order_by" onchange="this.form.submit()" class="w-full mb-2 p-2 bg-gray-700 text-white rounded">
                    <option value="price-ASC" selected>Prijs oplopend</option>
                    <option value="price-DESC">Prijs aflopend</option>
                    <option value="name-ASC">Naam oplopend</option>
                    <option value="name-DESC">Naam aflopend</option>
                </select>

                <label class="block text-sm" for="products_on_page">Selecteer het aantal producten:</label>
                <select name="products_on_page" onchange="this.form.submit()" class="w-full p-2 bg-gray-700 text-white rounded">
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </form>
    </div>

    <div id="ResultsArea" class="md:w-2/3 m-1 md:m-10">
        <?php if (isset($Result['data']) && $amount > 0) : ?>
            <?php foreach ($Result['data'] as $row) : ?>
                <div class="mb-4 p-6 bg-gray-800 text-white rounded-md shadow-md overflow-hidden w-full">
                    <?php if (isset($row['ImagePath'])) : ?>
                        <div class="w-48 h-48 bg-cover bg-center rounded-md float-left mr-4" style="background-image: url('<?= "Public/StockItemIMG/" . $row['ImagePath'] ?>');"></div>
                    <?php elseif (isset($row['BackupImagePath'])) : ?>
                        <div class="w-48 h-48 bg-cover bg-center rounded-md float-left mr-4" style="background-image: url('<?= "Public/StockGroupIMG/" . $row['BackupImagePath'] ?>');"></div>
                    <?php endif; ?>

                    <div class="flex items-center mb-2">
                        <div class="ml-auto text-gray-300">
                            <h1 class="text-lg font-bold"><?= sprintf("€%0.2f", berekenVerkoopPrijs($row['RecommendedRetailPrice'], $row['TaxRate'])) ?></h1>
                            <h6 class="text-sm">Inclusief BTW</h6>
                        </div>
                    </div>
                    <h1 class="text-gray-400 text-sm">Artikelnummer: <?= $row['StockItemID'] ?></h1>
                    <a href="productpage.php?id=<?= $row['StockItemID'] ?>" class="text-blue-400 hover:underline text-lg font-semibold"><?= $row['StockItemName'] ?></a>
                    <p class="text-gray-400 text-sm"><?= $row['MarketingComments'] ?></p>
                    <h4 class="text-green-400"><?= getVoorraadTekst($row['QuantityOnHand']) ?></h4>
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
            <h2 id="NoSearchResults" class="text-xl font-bold">
                Helaas, uw zoek opdracht voor
                <u><?= isset($_GET['search']) ? $_GET['search'] : '' ?></u>
                heeft geen resultaten opgeleverd.
            </h2>
        <?php endif; ?>
    </div>
</main>

<?php
include __DIR__ . "/footer.php";
?>