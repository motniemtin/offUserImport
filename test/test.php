<?php
require_once("../vendor/autoload.php");
include("../src/Motniemtin/offUserImport/offUserImport.php");
$offUserImport = new \Motniemtin\offUserImport\offUserImport();
$offUserImport->setStorePath("./");
$offUserImport->setTemplate("./Import_User_Template.csv");
$offUserImport->convertFile("gv.csv","Giao Vien");
