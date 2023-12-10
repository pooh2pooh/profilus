<?php

    if (isset($_POST['email']) or isset($_POST['password'])) {
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }