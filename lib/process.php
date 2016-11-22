<?php
ini_set('display_errors','on');
error_reporting(E_ALL);
require_once('../vendor/autoload.php');

// Obviously there is way better error / validation checking, but I'm sure you get the point. Currently I will just assume we are a safe user and the input is valid.
if(!isset($_FILES['csv']) || !isset($_POST['format'])) header('HTTP/1.0 403 Forbidden');

$format = $_POST['format']; // again, see security disclaimer above..
$CafeMedia = new CafeMedia\CafeMedia($_FILES["csv"]["tmp_name"], $format);

$topPosts = $CafeMedia->getTopPosts();
$CafeMedia->generate($topPosts, "top_posts");

$otherPosts = $CafeMedia->getOtherPosts();
$CafeMedia->generate($otherPosts, "other_posts");

$topDailyPosts = $CafeMedia->exportDailyTopPost(false,  $_POST['date']);
$CafeMedia->generate($topDailyPosts,"daily_top_posts");

$CafeMedia->returnTo('../?success&format='.$format);