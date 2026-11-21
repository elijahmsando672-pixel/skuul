<?php
$start = microtime(true);
$conn = @mysqli_connect("skuul-mysql", "sail", "secret", "skuul", 3306);
if (!$conn) {
    echo "DB_FAIL: " . mysqli_connect_error() . "\n";
} else {
    echo "DB_OK\n";
    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema='skuul'");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Tables: " . $row['cnt'] . "\n";
    }
    mysqli_close($conn);
}
echo "Time: " . round(microtime(true) - $start, 2) . "s\n";