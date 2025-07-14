<?php

    namespace classes;

    use Exception;

    class GestoreProfiloStudente{
        //nel caso reale, l'anagrafica e la carriera dei laureandi dovranno essere recuperati da un server apposito attraverso una richiesta HTTP
        private static array $anagrafica = [];
        private static array $carriera = [];

        public function __construct() {
        }

        /**
         * Restituisce l'anagrafica del laureando
         * @param int matricola
         * @return string
         */
        public static function restituisciAnagrafica(int $matricola) : array {
            //creo un vettore contenente le informazioni anagrafiche del laureando
            if (!isset(self::$anagrafica[$matricola]))
                self::$anagrafica[$matricola] = json_decode(file_get_contents(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "anagrafica_studenti.json"), true)[$matricola]["Entries"]["Entry"];

            if (!isset(self::$anagrafica[$matricola])){
                throw new \Exception("Matricola $matricola assente!");
            }

            return self::$anagrafica[$matricola];
        }

        /**
         * Restituisce la carriera del laureando costituita dagli esami da quest'ultimo sostenuti
         * @param int matricola
         * @return string
         */
        public static function restituisciCarriera (int $matricola) : array {
            //creo un vettore contenente le informazioni anagrafiche del laureando
            if(!isset(self::$carriera[$matricola]))
                self::$carriera[$matricola] = json_decode(file_get_contents(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "carriera_studenti.json"), true)[$matricola]["Esami"]["Esame"];

            if (!isset(self::$carriera[$matricola])){
                throw new \Exception("Matricola $matricola assente!");
            }

            return self::$carriera[$matricola];
        }
    }
    
?>