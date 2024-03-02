<?php

    define('HOST', 'localhost');
    define('USER', 'root');
    define('PASSWORD', '');
    define('DB', 'restapi_tugas');

    $connection = mysqli_connect( HOST, USER, PASSWORD, DB );

    if (!$connection) {
        die('Koneksi gagal: ' . mysqli_connect_error());
    }

?>
