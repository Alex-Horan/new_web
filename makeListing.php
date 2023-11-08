<?php
require 'db_connection.php';
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

//web page php template start
//
//
//This is what will be written into every php file created
$template = "
<?php 
\$fname = explode('.', __FILE__)[0];
\$fname .= '.json';
\$jc = file_get_contents(\$fname);
\$jd = json_decode(\$jc);
\$name = \$jd->name;
\$desc = \$jd->description;
\$price = \$jd->price;
\$item_id = \$jd->id;
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?php \$name ?></title>
</head>
<body>
    <div>
        <h1> <?php echo \$name; ?></h1>
        <p><?php echo \$desc; ?></p>
        <h3>$<?php echo \$price; ?></h3>
    </div>


</body>
</html>

";



// template end


$db = new MyDB();

if ((!empty($name)) && (!empty($desc)) && (!empty($price))) {
    $randId = generateRandomString(10);
    $ji = array('name' => $name, 'description'=> $desc,'price'=> $price, 'id' => $randId); //creates array to be turned into
    $je = json_encode($ji); //turns $ji array into json object
        if (is_dir("./item_pages/$name/$name.json") || is_dir("./item_pages/$name/$name.php") || is_dir("./item_pages/$name")) {
            echo "goof";
        } else {
            mkdir("./item_pages/$name", 0760, true); //changes perms so files can be created, might need to change back to either 
            $jf = fopen("./item_pages/$name/$name.json","c"); //creates json file for each listing posted
            $pf = fopen("./item_pages/$name/$name.php","c"); //creates php file for each listing posted


            fwrite($jf, $je); //writes the json object to the newly created $name/$name.json file
            fwrite($pf, $template); //adds the html/php template to the newly created php file

            $db->exec("INSERT INTO listings(name, descript, price, randID) VALUES ('$name', '$desc', '$price', '$randId')"); //after sanitizing and validating the data, this inserts it into the sqlite3 database
            $db->close(); //closes the connection between database CRUD, possibly slower? but makes it much less vulnerable. if too slow will fix in future

            fclose($jf); //closes the file connection, leaving it open would be unnecessary and insecure
            fclose($pf); //same as above

            $jdata[] = array("./$name/$name.php"); //creaates an array of the newly created php file
            $tmp = fopen("./fpaths.json", "a");
            fclose($tmp);
            if (!file_get_contents("./fpaths.json")) { //checks if there is already a json object 
                // echo "good";
                $ejda = json_encode($jdata);
                file_put_contents("./fpaths.json", $ejda); //puts the encoded json object into the path file
                header("Location: ./item_pages/$name/$name.php");
            } else {
                // echo "bad";
                $tmpInp = file_get_contents("./fpaths.json"); // if there is already an object in the paths
                $tmpData = json_decode($tmpInp);
                array_push($tmpData, $jdata); // this is what adds the comma when adding multiple json objects
                $finData = json_encode($tmpData);
                file_put_contents("./fpaths.json", $finData);

                header("Location: ./item_pages/$name/$name.php");
            }
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
        Description: <span>* <?php echo $desc; ?></span><br><textarea name="description" placeholder="Please enter a description" cols="30" rows="10"></textarea><br><br>
        Price: <input type="number" id="price" name="price" step=".01" pattern="/[0-9]/" max='9999.99' ><span>* <?php echo $price; ?></span> <br><br>
        <input type="submit" value="submit" name="submit">

    </form>
</body>
</html>