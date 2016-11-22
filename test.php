<?php
require_once('vendor/autoload.php');
use Carbon\Carbon;
$today = "Sat Oct 03 04:05:55 2015";


$timestamp = strtotime($today);

$carbon = Carbon::createFromTimestamp($timestamp);

print_r($carbon);




