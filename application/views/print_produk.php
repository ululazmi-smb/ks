<html lang="en"><head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            margin-left: -8.7;
        }
        #barcode {
            width: 38mm;
  height: 13mm;
  padding: 1mm;
  overflow: hidden;
  display: inline-block;
  margin-bottom: -1.5mm;
}

    </style>
</head>
<body>
    <?php
  //  var_dump($produk);
    for($i=0;$i<count($produk);$i++)
    {
    ?>
    <div id="barcode">
        <center>
                <p style="margin: 0;background: transparent;font-size: 3mm;"><?=$produk[$i]["nama"]?></p>
                <img alt="testing" style="width: 35mm;" src="<?=base_url()?>barcode.php?text=<?=$produk[$i]["barcode"]?>">
                <p style="margin: 0mm;font-size: 3mm;"><?=$produk[$i]["barcode"]?></p>
        </center>
    </div>
    <?php
        }
    ?>
</body>
</html>