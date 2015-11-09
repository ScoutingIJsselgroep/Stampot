<?php
$link = mysql_connect('localhost', 'stampot', 'b52afe50-c22f-40c9-b240-a90f680e2dbb');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("stampotscouting-ijsselgroepnl");
