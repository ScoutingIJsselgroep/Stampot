<?php
//header("HTTP/1.0 404 Not Found");
//die();

die(json_encode(array('now' => date('d-m-Y H:i:s'))));
