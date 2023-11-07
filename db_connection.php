<?php
    class MyDB extends SQLite3 {
        public function __construct() {
            $this->open("./shop_items.sqlite");
        }
    }
?>