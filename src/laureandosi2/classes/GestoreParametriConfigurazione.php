<?php

    namespace classes;

    class GestoreParametriConfigurazione {
        private static array $esami_informatici = [];
        private static array $filtro_esami = [];
        private static array $parametri = [];

        public function __construct() {
        }
        
        /**
         * Restituisce gli esami appartenenti al settore scientifico-disciplinare ING-INF/05
         * @return array
         */
        public static function restituisciEsamiInformatici() : array {
            if (!isset(self::$esami_informatici))
                self::$esami_informatici = json_decode(file_get_contents(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "config". DIRECTORY_SEPARATOR. "esami_informatici.json"), true);
            return self::$esami_informatici;
        }

        /**
         * Restituisce il filtro esami relativo al Cdl specificato
         * @param string $cdl
         * @return array
         */
        public static function restituisciFiltroEsami(string $cdl) : array {
            if (!isset(self::$filtro_esami[$cdl]))
                self::$filtro_esami[$cdl] = json_decode(file_get_contents(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "filtro_esami.json", true), true)[$cdl];
            return self::$filtro_esami[$cdl];
        }

        /**
         * Restituisce i parametri del Cdl
         * @param string $cdl
         * @return array
         */
        public static function restituisciParametriCdl(string $cdl) : array {
            if (!isset(self::$parametri[$cdl]))
                self::$parametri[$cdl] = json_decode(file_get_contents(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "config". DIRECTORY_SEPARATOR. "corsi_di_laurea.json"), true)[$cdl];
            return self::$parametri[$cdl];
        }
    }

?>