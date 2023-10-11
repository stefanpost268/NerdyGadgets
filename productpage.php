<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
   include __DIR__ . "/header.php";
   $imageUrl = getStockItemImage($_GET['id'], $databaseConnection);
   $item = getStockItem($_GET['id'], $databaseConnection);
   if (count($imageUrl) > 0) {
      $imagePath = $imageUrl[0]['ImagePath'];
   } else {
      $imagePath = null;
   }
?>

<div class="container">
   <div class="row">
      <div class="col-md-6">
         <h1 class="display-4"><?php print($item["StockItemName"]) ?></h1>
         <p class="lead"><?php print($item["SearchDetails"]); ?></p>
      </div>
      <div class="col-md-6">
         <p>Product prijs: €<?php print(round($item["SellPrice"], 2)); ?></p>
         <p><?php print($item["QuantityOnHand"]); ?></p>
      </div>
      <div>
      <div class="warning">
         <img width='300px' height='300px' src="<?php 
         if($imagePath != null) {
            print("Public/StockItemIMG/" . $imagePath);
         } else {
            print("Public/StockGroupIMG/Toys.jpg");
         }
         
         ?>" />
   </div>
</div>

<?php
include __DIR__ . "/footer.php";
?>

