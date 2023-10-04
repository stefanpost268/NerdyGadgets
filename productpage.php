<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
   include __DIR__ . "/header.php";

   $item = getStockItem($_GET['id'], $databaseConnection)
?>
<div class="IndexStyle">
   <h1>
      <?php print($item["StockItemName"]) ?>
   </h1>
   <p>
      <?php print($item["SearchDetails"]); ?>
   </p>
   <p>
      Product Price: €<?php print(round($item["SellPrice"], 2)); ?>
   </p>


</div>
<?php
include __DIR__ . "/footer.php";
?>

