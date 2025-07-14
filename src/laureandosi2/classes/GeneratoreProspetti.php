<?php

    namespace classes;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "Laureando.php";
    require_once __DIR__ . DIRECTORY_SEPARATOR . "LaureandoInformatica.php";
    require_once __DIR__ . DIRECTORY_SEPARATOR . "ProspettoCommissione.php";

    class GeneratoreProspetti {
        private array $matricole;
        private string $cdl;
        private string $dataLaurea;

        public function __construct(array $matricole, string $cdl, string $dataLaurea) {
            $matricole = array_filter($matricole ?? [], function ($element) {
                return !empty($element);
            });

            if (is_null($matricole) || empty($matricole))
                throw new InvalidArgumentException("Matricole non inserite");

            $this->matricole = $matricole;
            $this->dataLaurea = $dataLaurea;
            $this->cdl = $cdl;
        }

        /**
         * Genera i prospetti per tutti i laureandi e quello destinato alla commissione
         * @param bool $test
         * @return void
         */
        public function generaProspetti(bool $test) : void {
            $path = ($test) ? (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "test_generati") : (dirname(__DIR__, 1). DIRECTORY_SEPARATOR. "prospetti_generati"); 
            $path .= DIRECTORY_SEPARATOR. $this->cdl. DIRECTORY_SEPARATOR. $this->dataLaurea;
            
            if (!file_exists($path))
                mkdir($path, 0777, true);

            $laureandi = array();
            foreach ($this->matricole as $matricola) {
                if ($this->cdl == "t-inf")
                    $laureando = LaureandoInformatica::getInstanceInfo($matricola, $this->cdl, $this->dataLaurea);
                else
                    $laureando = Laureando::getInstance($matricola, $this->cdl);

                $laureandi[] = $laureando;
            }

            $prospettoCommissione = new ProspettoCommissione($laureandi, $this->dataLaurea);
            $prospettoCommissione->genera($test);
            $prospettoCommissione->salva($test);
        }
    }

?>