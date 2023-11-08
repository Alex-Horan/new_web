<?php
$name = $desc = $price = '';
$nameErr = $descErr = $priceErr = '';





if ($_SERVER["REQUEST_METHOD"]=='POST') { //validates and sanitizes each piece of data from the html form
    if (empty($_POST["title"])) {
        $nameErr = "Please enter an item name";
    } else {
        $name = testinput($_POST["title"]);
    }
    if (empty($_POST['description'])) {
        $descErr = "Please enter an item description";
    } else {
        $desc = testinput($_POST["description"]);
    }
    if (empty($_POST['price'])) {
        $priceErr = "Please enter a price";
    } else {
        $price = testinput($_POST["price"]);
    }
}
function testinput(string $data) {
    $data = trim($data); //removes whitespace from start and end of data
    $data = stripslashes($data); //removes any slashes from the data
    $data = htmlspecialchars($data); //prevents any html escape codes from being entered, changes it to html special char notation so sql injection is mitigated, at least through the html form
    return $data;
}
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )), 1, $length); //thank you to A. Cheshirov on stack overflow for providing this code for the string gen https://stackoverflow.com/a/13212994
}


if ((!empty($name)) && (!empty($desc)) && (!empty($price))) {
    $ji = array('name' => $name, 'description' => $desc, 'price'=> $price, 'id' => generateRandomString(10)); //creates an associative array to turn into json object
    $je = json_encode($ji);
        if (is_dir("./item_pages/$name/$name") || is_dir("./item_pages/$name")) {
            echo "goof"; //im honestly not sure, if this runs then i should probably remove it
            //normally id just like it to skip the file or cancel the creation
        } else {
            mkdir("./item_pages/$name");
            $jf = fopen("./item_pages/$name/$name.json", 'c'); //creates json file for each page created
            $pf = fopen("./item_pages/$name/$name.php", 'c'); //creates php file for each page created
            fwrite($jf, $je);
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Listing</title>
    <script src="./jquery-3.7.1.js"></script>
</head>
<body>

    <script>
        $(document).ready(function() {
            $("#price").on("change", function() {
                this.value = parseFloat(this.value).toFixed(2);
                let part2 = (this.value).split(".")[1].toString();
                let part1 = (this.value).split(".")[0].toString();

                if (part1.length > 4) {
                    part1 = part1.substring(0, part1.length-1);
                }

                if ((part1.length <= 4 ) && ( part2.length == 2)) {
                    this.value = parseFloat(part1 + "." + part2);
                }
            });
        });
    </script>
    
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        Name: <input type="text" name="title" ><span>* <?php echo $name; ?></span> <br><br>
        Description: <span>* <?php echo $descErr; ?></span><br><textarea name="description" placeholder="Please enter a description" cols="30" rows="10"></textarea><br><br>
        Price: <input type="number" id="price" name="price" step=".01" pattern="/[0-9]/" max='9999.99' ><span>* <?php echo $priceErr; ?></span> <br><br>
        <input type="submit" value="submit" name="submit">

    </form>
</body>
</html>