<?php

    require_once __DIR__ . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "GUI.php";

    use classes\GUI;

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["API"]) && $_GET["API"] === "creaProspetti") {
        $json = file_get_contents("php://input");
        echo GUI::creaProspetti($json);
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["API"]) && $_GET["API"] === "accediProspetti") {
        $corso_laurea = $_GET["cdl"];
        $data_laurea = $_GET["data"];

        GUI::accediProspetti($corso_laurea, $data_laurea);
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["API"]) && $_GET["API"] === "inviaProspetto") {
        $json = file_get_contents("php://input");
        echo GUI::inviaProspetto($json);
    }

?>