<?php 
$fname = explode('.', __FILE__)[0];
$fname .= '.json';
$jc = file_get_contents($fname);
$jd = json_decode($jc);
$name = $jd->name;
$desc = $jd->description;
$price = $jd->price;
$item_id = $jd->id;
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?php $name ?></title>
</head>
<body>
    <div>
        <h1> <?php echo $name; ?></h1>
        <p><?php echo $desc; ?></p>
        <h3>$<?php echo $price; ?></h3>
    </div>


</body>
</html>
