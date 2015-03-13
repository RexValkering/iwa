<?php
echo "hi";
require_once("../include/includes.php");

echo "hi";

session_start();
session_destroy();
//header("Location: " . site_root() . "index.php");
//exit(0);