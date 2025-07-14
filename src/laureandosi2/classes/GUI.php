<?php

    namespace classes;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "GeneratoreProspetti.php";
    require_once __DIR__ . DIRECTORY_SEPARATOR . "GestoreInvioProspetti.php";

    class GUI {
        private function __construct(){
        }
        
        /**
         * Crea i prospetti destinati alla commissione e per i laureandi per il cdl e la data di laurea specificati
         * @param string $json
         * @param bool $test
         * @return string
         */
        public static function creaProspetti(string $json, bool $test = false) : string {
            $dati = json_decode($json, true);

            try {
                $generatore = new GeneratoreProspetti($dati["array_matricole"], $dati["cdl"], $dati["data"]);
                $generatore->generaProspetti($test);
            }
            catch (Exception $e) {
                http_response_code(500);
                return json_encode(["error" => $e->getMessage(), "success" => false]);
            }

            http_response_code(200);
            return json_encode(["message" => "Prospetti creati", "success" => true]);
        }

        /**
         * Accede al prospetto destinato alla commissione per il cdl e la data di laurea specificati
         * @param string $cdl
         * @param string $dataLaurea
         * @return void
         */
        public static function accediProspetti(string $cdl, string $dataLaurea) : void {
            $filename = dirname(__DIR__ , 1) . DIRECTORY_SEPARATOR . "prospetti_generati" . DIRECTORY_SEPARATOR . $cdl . DIRECTORY_SEPARATOR . $dataLaurea . DIRECTORY_SEPARATOR . "commissione-" . $cdl . "-" . $dataLaurea . ".pdf";

            if (file_exists($filename)) {
                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="prospetti.pdf"');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($filename));
                header('Accept-Ranges: bytes');
                readfile($filename);

                http_response_code(200);
            }
            else {
                http_response_code(404);
                echo json_encode(["error" => "Prospetti non trovati", "success" => false]);
            }        
            exit;
        }

        /**
         * Invia il prospetto per il laureando specificato
         * @param string $json
         * @param string|null $indirizzo_test
         * @return string
         */
        public static function inviaProspetto(string $json, string $indirizzo_test = null) : string{ 
            $dati = json_decode($json, true);

            try {
                $invioMail = new GestoreInvioProspetti($dati["matricola"], $dati["cdl"], $dati["data"]);
                $invioMail->inviaProspetto($indirizzo_test);
            }
            catch (Exception $e) {
                http_response_code(500);
                return json_encode(["error" => $e->getMessage(), "success" => false]);
            }

            http_response_code(200);
            return json_encode(["success" => true]);
        }
    }

?>