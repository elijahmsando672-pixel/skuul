<?php
$conn = @mysqli_connect("skuul-mysql", "sail", "secret", "skuul", 3306);
if ($conn) { echo "DB_OK"; mysqli_close($conn); } else { echo "DB_FAIL: " . mysqli_connect_error(); }