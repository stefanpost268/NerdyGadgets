<div class="flex flex-col items-center justify-center text-center bg-gray-800 p-8 rounded-lg shadow-md">
    <h1 class="text-white text-4xl font-bold mb-4">Geen producten gevonden</h1>
    <img class="bg-white rounded-xl shadow-md" src="./Public/SVG/not-found.svg" alt="Empty Cart" width="150" height="150">
    <p class="text-gray-300 mt-4">
        Helaas, uw zoek opdracht voor
        <u>
            <?php print(isset($_GET['search']) ? $_GET['search'] : ''); ?>
        </u>
        heeft geen resultaten opgeleverd.
    </p>
</div>