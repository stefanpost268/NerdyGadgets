<!-- dit bestand bevat alle code voor de pagina die categorieën laat zien -->
<?php

include __DIR__ . "/header.php";
$StockGroups = getStockGroups($databaseConnection);

?>

<div id="Wrap" class="flex flex-wrap justify-center max-w-screen-2xl mx-auto">
    <?php if (isset($StockGroups)) {
        $i = 0;
        foreach ($StockGroups as $StockGroup) {
            if ($i < 6) {
                ?>
                <a href="<?php print "browse.php?category_id=" . $StockGroup["StockGroupID"]; ?>"
                   class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4 p-4">
                    <div id="StockGroup<?php print $i + 1; ?>"
                         class="bg-cover bg-center h-64 relative rounded-md overflow-hidden"
                         style="background-image: url('Public/StockGroupIMG/<?php print $StockGroup["ImagePath"]; ?>')">
                        <h1 class="text-white absolute bottom-4 left-4 text-lg font-bold">
                            <?php print $StockGroup["StockGroupName"]; ?>
                        </h1>
                    </div>
                </a>
                <?php
            }
            $i++;
        }
    } ?>
</div>


