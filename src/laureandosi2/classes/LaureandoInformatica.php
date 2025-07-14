<?php
    namespace classes;

    use DateTime;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "Laureando.php";
    
    class LaureandoInformatica extends Laureando {
        private static array $instancesInfo = [];
        public bool $bonus;
        
        public function __construct (int $matricola, string $cdl, string $dataLaurea) {
            parent::__construct($matricola, $cdl);
            $this->bonus = $this->dirittoAlBonus($dataLaurea);
        }

        /**
         * Restituisce l'istanza del laureando
         * @param int $matricola
         * @param string $cdl
         * @param string $dataLaurea
         * @return LaureandoInformatica
         */
        public static function getInstanceInfo(int $matricola, string $cdl, string $dataLaurea) : LaureandoInformatica {
            if (!isset(self::$instancesInfo[$matricola][$dataLaurea]))
                self::$instancesInfo[$matricola][$dataLaurea] = new LaureandoInformatica($matricola, $cdl, $dataLaurea);
            return self::$instancesInfo[$matricola][$dataLaurea];
        }

        /**
         * Restituisce la media pesata relativa agli esami informatici
         * @return float
         */
        public function calcolaMediaInformatica() : float {
            $somma = 0;
            $CFU = 0;
            foreach ($this->esami as $esame){
                if ($esame->informatico){
                    $somma += ($esame->voto * $esame->cfu);
                    $CFU += $esame->cfu;
                }
            }

            if (!$CFU) //evito una divisione per 0
                return 0;
            return round($somma/$CFU, 3);
        }

        /**
         * Retsituisce l'esame bonus qualora il laureando abbia diritto al bonus
         * @param string $data
         * @return bool
         */
        private function dirittoAlBonus(string $data) : bool {
            $dataLaurea = new DateTime($data);
            $data_limite = date_create($this->annoImmatricolazione + 4 . "-04-30");
            if ($dataLaurea > $data_limite)
                return false;

            //se siamo arrivati qui significa che il laureando ha diritto al bonus
            //dobbiamo determinare quale esame ha diritto al bonus
            $pos = 0;
            for ($i = 0; $i < count($this->esami); $i++){
                if (!$this->esami[$i]->in_avg)
                    continue;
                
                if (!$pos || ($this->esami[$pos]->voto > $this->esami[$i]->voto) || (($this->esami[$pos]->voto == $this->esami[$i]->voto) && ($this->esami[$pos]->cfu < $this->esami[$i]->cfu)))
                    $pos = $i;
            }

            $this->esami[$pos]->in_avg = false;
            return true;
        }
    }

?>