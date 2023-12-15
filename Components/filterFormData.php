<form id="sideBar" class="text-white">
    <input type="hidden" name="category_id" id="category_id" value="<?= isset($_GET['category_id']) ? $_GET['category_id'] : '' ?>">
    <div class="mt-2">
        <label for="search" class="block text-sm">Zoeken:</label>
        <input type="text" name="search" id="search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" class="w-full mb-2 p-2 bg-gray-700 text-white rounded">

        <label class="block text-sm" for="order_by">Sorteer op:</label>
        <select name="order_by" id="order_by" onchange="this.form.submit()" class="w-full mb-2 p-2 bg-gray-700 text-white rounded">
            <option value="price-ASC" <?php print($orderByLabel == "price-ASC" ? "selected" : ""); ?>>Prijs oplopend</option>
            <option value="price-DESC" <?php print($orderByLabel == "price-DESC" ? "selected" : ""); ?>>Prijs aflopend</option>
            <option value="name-ASC" <?php print($orderByLabel == "name-ASC" ? "selected" : ""); ?>>Naam oplopend</option>
            <option value="name-DESC" <?php print($orderByLabel == "name-DESC" ? "selected" : ""); ?>>Naam aflopend</option>
        </select>

        <label class="block text-sm" for="products_on_page">Selecteer het aantal producten:</label>
        <select name="products_on_page" onchange="this.form.submit()" class="w-full p-2 bg-gray-700 text-white rounded">
        <?php foreach ($config->productsOnPageOptions as $products) { ?>
            <option value="<?= $products ?>" <?php print($productsOnPage == $products ? "selected" : ""); ?>><?= $products ?></option>
        <?php } ?>
        </select>
    </div>
</form>