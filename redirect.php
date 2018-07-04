<?php

$params = http_build_query($_REQUEST);
header("Location: /index.php?{$params}");
