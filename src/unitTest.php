<?php

    $classes_path = __DIR__ . DIRECTORY_SEPARATOR . "laureandosi2" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR;
    require_once $classes_path  . "GUI.php";
    require_once $classes_path . "Laureando.php";
    require_once $classes_path . "LaureandoInformatica.php";

    class UnitTest {
        public classes\Laureando $laureando;
        public $input;
        public $expectedOutput;
        public $functionToExecute;
        public $actualOutput;

        public function __construct($input, $expectedOutput, $functionToExecute) {
            $this->laureando = classes\Laureando::getInstance((int)$input["matricola"], $input["cdl"]);
            $this->input = $input;
            $this->expectedOutput = $expectedOutput;
            $this->functionToExecute = $functionToExecute;

            echo "Input: " . json_encode($this->input) . "<br>" . "Expected output: " . json_encode($this->expectedOutput) . " | ";            
            $this->execute();
        }

        /**
         * Esegue il test
         * @return void
         */
        private function execute(){
            $this->actualOutput = call_user_func($this->functionToExecute, $this->input);

            $success = ($this->actualOutput === $this->expectedOutput);
            echo "Output: " . json_encode($this->actualOutput);
            echo '<p style="color: ' . ($success ? 'green' : 'red') . ';">';
            echo $success ? "Success" : "Fail";
            echo '</p>';
            echo "<br>";
        }
    }
?>


<!DOCTYPE html>
<html lang="it-IT">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="test.css">
        <title>Gestione Prospetti di Laurea: Test</title>
    </head>
    <body>
        <div class="container">
            <h1>Gestione Prospetti di Laurea - Unit Test</h1>
            <br>

            <h3>Calcolo della media</h3>
            <?php
                function testMedia($dati) {
                    if ($dati["cdl"] != "t-inf")
                        $laureando = classes\Laureando::getInstance($dati["matricola"], $dati["cdl"]);
                    else
                        $laureando = classes\LaureandoInformatica::getInstanceInfo($dati["matricola"], $dati["cdl"], $dati["dataLaurea"]);

                    return round($laureando->calcolaMediaPesata(), 3);
                }

                $test = new UnitTest(array("matricola" => "123456", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 23.655, "testMedia");
                $test = new UnitTest(array("matricola" => "234567", "cdl" => "m-ele", "dataLaurea" => "2023-01-01"), 24.559, "testMedia");
                $test = new UnitTest(array("matricola" => "345678", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 25.564, "testMedia");
                $test = new UnitTest(array("matricola" => "456789", "cdl" => "m-tel", "dataLaurea" => "2023-01-01"), 32.625, "testMedia");
                $test = new UnitTest(array("matricola" => "567890", "cdl" => "m-cyb", "dataLaurea" => "2023-01-01"), 24.882, "testMedia");
            ?>

            <h3>Calcolo della media di informatica</h3>
            <?php
                function testMediaInf($dati) {
                    $laureando = classes\LaureandoInformatica::getInstanceInfo($dati["matricola"], $dati["cdl"], $dati["dataLaurea"]);
                    return round($laureando->calcolaMediaPesata(), 3);
                }

                $test = new UnitTest(array("matricola" => "123456", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 23.655, "testMediaInf");
                $test = new UnitTest(array("matricola" => "345678", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 25.564, "testMediaInf");
                $test = new UnitTest(array("matricola" => "678901", "cdl" => "t-inf", "dataLaurea" => "2023-01-04"), 26.517, "testMediaInf");
                $test = new UnitTest(array("matricola" => "999999", "cdl" => "t-inf", "dataLaurea" => "2023-01-04"), 24.741, "testMediaInf");
            ?>

            <h3>Calcolo dei crediti totali</h3>
            <?php
                function testCrediti($dati) {
                    if ($dati["cdl"] != "t-inf")
                        $laureando = classes\Laureando::getInstance($dati["matricola"], $dati["cdl"]);
                    else
                        $laureando = classes\LaureandoInformatica::getInstanceInfo($dati["matricola"], $dati["cdl"], $dati["dataLaurea"]);

                    return $laureando->restituisciCFU();
                }

                $test = new UnitTest(array("matricola" => "123456", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 177, "testCrediti");
                $test = new UnitTest(array("matricola" => "234567", "cdl" => "m-ele", "dataLaurea" => "2023-01-01"), 102, "testCrediti");
                $test = new UnitTest(array("matricola" => "345678", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 177, "testCrediti");
                $test = new UnitTest(array("matricola" => "456789", "cdl" => "m-tel", "dataLaurea" => "2023-01-01"), 96, "testCrediti");
                $test = new UnitTest(array("matricola" => "567890", "cdl" => "m-cyb", "dataLaurea" => "2023-01-01"), 120, "testCrediti");
            ?>

            <h3>Calcolo dei crediti che fanno media</h3>
            <?php
                function testCreditiMedia($dati) {
                    if ($dati["cdl"] != "t-inf")
                        $laureando = classes\Laureando::getInstance($dati["matricola"], $dati["cdl"]);
                    else
                        $laureando = classes\LaureandoInformatica::getInstanceInfo($dati["matricola"], $dati["cdl"], $dati["dataLaurea"]);

                    return $laureando->restituisciCFUinAVG();
                }
      
                $test = new UnitTest(array("matricola" => "123456", "cdl" => "t-inf", "dataLaurea" => "2020-01-01"), 165, "testCreditiMedia");
                $test = new UnitTest(array("matricola" => "234567", "cdl" => "m-ele", "dataLaurea" => "2023-01-01"), 102, "testCreditiMedia");
                $test = new UnitTest(array("matricola" => "345678", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), 165, "testCreditiMedia");
                $test = new UnitTest(array("matricola" => "456789", "cdl" => "m-tel", "dataLaurea" => "2023-01-01"), 96, "testCreditiMedia");
                $test = new UnitTest(array("matricola" => "567890", "cdl" => "m-cyb", "dataLaurea" => "2023-01-01"), 102, "testCreditiMedia");
            ?>

            <h3>Calcolo del bonus</h3>
            <?php
                function testBonus($dati) {
                    $laureando = classes\LaureandoInformatica::getInstanceInfo($dati["matricola"], $dati["cdl"], $dati["dataLaurea"]);
                    return $laureando->bonus;
                }

                $test = new UnitTest(array("matricola" => "123456", "cdl" => "t-inf", "dataLaurea" => "2023-01-01"), false, "testBonus");
                $test = new UnitTest(array("matricola" => "345678", "cdl" => "t-inf", "dataLaurea" => "2023-04-30"), true, "testBonus");
                $test = new UnitTest(array("matricola" => "345678", "cdl" => "t-inf", "dataLaurea" => "2020-01-01"), true, "testBonus");
                $test = new UnitTest(array("matricola" => "678901", "cdl" => "t-inf", "dataLaurea" => "2025-01-04"), false, "testBonus");
                $test = new UnitTest(array("matricola" => "999999", "cdl" => "t-inf", "dataLaurea" => "2025-01-04"), false, "testBonus");
            ?>
        </div>
    </body>
</html>
