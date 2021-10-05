<?php

    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/satellites/customer/php/model/*.php') AS $models) { require($models); }
    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/satellites/customer/php/view/*.php') AS $views) { require($views); }
    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/satellites/customer/php/controller/*.php') AS $controllers) { require($controllers); }

?>