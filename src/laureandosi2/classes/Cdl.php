<?php

    namespace classes;

    class Cdl {
        private static array $instances = [];
        public string $cdl;
        public string $cdl_alt;
        public string $cdl_short;
        public string $voto_laurea;
        public int $TOT_CFU;
        public int $lode;
        public array $par_T;
        public array $par_C;
        public string $msg_commissione;
        public string $txt_email;

        public function __construct (array $parametri) {
            $this->cdl = $parametri["cdl"];
            $this->cdl_alt = $parametri["cdl-alt"];
            $this->cdl_short = $parametri["cdl-short"];
            $this->voto_laurea = $parametri["voto-laurea"];
            $this->TOT_CFU = (int)$parametri["tot-CFU"];
            $this->lode = $parametri["lode"];
            $this->par_T = $parametri["par-T"];
            $this->par_C = $parametri["par-C"];
            $this->msg_commissione = $parametri["msg-commissione"];
            $this->txt_email = $parametri["txt-email"];
        }

        /**
         * Restituisce l'istanza di Cdl
         * @param array $parametri
         * @return Cdl
         */
        public static function getInstance(array $parametri) : Cdl {
            $cdl_short = $parametri["cdl-short"];
            if (!isset(self::$instances[$cdl_short])) {
                self::$instances[$cdl_short] = new Cdl($parametri);
            }

            return self::$instances[$cdl_short];
        }
    }

?>