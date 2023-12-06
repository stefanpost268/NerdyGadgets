<?php include __DIR__ . "/header.php";

    $products = getRandomProducts($databaseConnection);
?>
<main class="relative flex flex-col items-center justify-start w-full overflow-hidden min-h-screen bg-gradient-to-b from-gray-800 to-black --geist-foreground:#000">
    <h1 class="mt-6 lg:!mt-12 mx-6 w-[300px] md:w-full font-extrabold text-4xl sm:text-5xl md:text-6xl leading-tight xl:leading-snug text-center pb-4 bg-clip-text text-transparent bg-gradient-to-b from-black/80 to-black from-white to-[#AAAAAA]">
        NerdyGadgets
    </h1>
    <p class="mx-6 text-lg sm:text-xl md:text-2xl sm:mt-4 mb-8 max-h-[112px] md:max-h-[96px] w-[315px] md:w-[660px] font-space-grotesk text-center text-[#666666] text-[#888888]">
        Welcome to NerdyGadgets, the best place to buy your gadgets!
    </p>

    <div id="controls-carousel" class="relative w-full max-w-[660px] mb-8 px-4 sm:px-8" data-carousel="static">
    <!-- Carousel wrapper -->
    <div class="relative h-56 overflow-hidden rounded-lg md:h-96 bg-gradient-to-b from-gray-900 to-black from-gray-800 to-black">
        <?php foreach($products as $product) { 
            $productImage = getProductImage($product["StockItemID"], $databaseConnection, $product);
            ?>
            <a href="productpage.php?id=<?php print($product["StockItemID"]); ?>">
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="<?php print($productImage); ?>" class="w-full h-full object-contain" alt="...">
                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-black bg-opacity-50 text-white">
                        <p class="text-lg font-semibold">Prijs: € <?php print(number_format($product["SellPrice"], 2)); ?></p>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
    <button type="button" class="text-white absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <img class="shadow-md" src="./Public/SVG/arrow-left.svg" alt="Empty Cart" width="30" height="30">
    </button>
    <button type="button" class="text-white absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <img class="shadow-md" src="./Public/SVG/arrow-right.svg" alt="Empty Cart" width="30" height="30">
    </button>
</div>

</main>











<?php include __DIR__ . "/footer.php"; ?>