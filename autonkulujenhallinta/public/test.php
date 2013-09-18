<?php
session_start();

$i = isset($_SESSION['testi']) ? $_SESSION['testi'] : 0;

$i++;

echo $i;

$_SESSION['testi'] = $i;