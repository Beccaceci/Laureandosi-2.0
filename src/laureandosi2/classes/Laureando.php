<?php

    namespace classes;

    use Exception;
    use DateTime;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "GestoreProfiloStudente.php";
    require_once __DIR__ . DIRECTORY_SEPARATOR . "GestoreParametriConfigurazione.php";
    require_once __DIR__ . DIRECTORY_SEPARATOR . "Esame.php";
    require_once __DIR__ . DIRECTORY_SEPARATOR . "Cdl.php";

    class Laureando {
        private static array $instances = [];
        public string $nome;
        public string $cognome;
        public int $matricola;
        public string $email;
        public array $esami;
        public Cdl $cdl;
        public int $annoImmatricolazione;

        public function __construct (int $matricola, string $cdl) {
            $anagrafica = GestoreProfiloStudente::restituisciAnagrafica($matricola);

            $this->nome = $anagrafica["nome"];
            $this->cognome = $anagrafica["cognome"];
            $this->matricola = $matricola;
            $this->email = $anagrafica["email_ate"];
            $parametri = GestoreParametriConfigurazione::restituisciParametriCdl($cdl);
            $this->cdl = Cdl::getInstance($parametri);
            $this->annoImmatricolazione = -1;
            
            $esami_informatici = GestoreParametriConfigurazione::restituisciEsamiInformatici();    

            //recupero il filtro esami relativo al corso di laurea del laureando
            $filtro_esami = GestoreParametriConfigurazione::restituisciFiltroEsami($cdl);
            $filtro_esami = array_values(array_filter(
                $filtro_esami,
                function ($mat_i) use ($matricola) {
                    return $mat_i == "*" || (int) $mat_i == $matricola;
                },
                ARRAY_FILTER_USE_KEY
            ));

            if (count($filtro_esami) == 2)
                $filtro_esami = array_merge_recursive($filtro_esami[0], $filtro_esami[1]);
            else
                $filtro_esami = $filtro_esami[0];

            //recupero la carriera dello studente
            $carriera = GestoreProfiloStudente::restituisciCarriera($matricola); 

            if (!count($carriera)){
                throw new Exception("Errore: Corso di laurea non corretto per la matricola $matricola.");
            }

            //aggiungo gli esami conseguiti dallo studente
            $this->esami = array();

            for ($i = 0; $i < count($carriera); $i++){
                if (defined("TEST") || $this->cdl->cdl_alt == $carriera[$i]["CORSO"]){
                    $esame_nome = $carriera[$i]["DES"];
                    $esame_voto = (!strcmp($carriera[$i]["VOTO"], "30  e lode")) ? $this->cdl->lode : (int)$carriera[$i]["VOTO"];
                    $esame_cfu = $carriera[$i]["PESO"];
                    $esame_data = $carriera[$i]["DATA_ESAME"];
                    $esame_previstoDalPianoStudi = defined("TEST") || !in_array($esame_nome, $filtro_esami["esami-non-cdl"]);
                    $esame_faMedia = defined("TEST") || ($esame_previstoDalPianoStudi && !in_array($esame_nome, $filtro_esami["esami-non-avg"]));
                    $esame_informatico = in_array($esame_nome, $esami_informatici);

                    if ($this->annoImmatricolazione == -1)
                        $this->annoImmatricolazione = (int)$carriera[$i]["ANNO_IMM"];

                    $this->esami[] = new Esame(
                        $esame_nome,
                        $esame_voto,
                        $esame_cfu,
                        $esame_data,
                        $esame_previstoDalPianoStudi,
                        $esame_faMedia,
                        $esame_informatico
                    );
                }
            }
            
            usort($this->esami, function($a, $b) {
                return ((DateTime::createFromFormat("d/m/Y", $a->data))->getTimestamp() - (DateTime::createFromFormat("d/m/Y", $b->data))->getTimestamp());
            }); 
        }

        /**
         * Restituisce l'istanza del laureando qualora esista, altrimenti ne crea una
         * @param int $matricola
         * @param string $cdl
         * @return Laureando
         */
        public static function getInstance(int $matricola, string $cdl) : Laureando {
            if (!isset(self::$instances[$matricola]))
                self::$instances[$matricola] = new Laureando($matricola, $cdl);
            return self::$instances[$matricola];
        }

        /**
         * Calcola la media pesata del voto dei soli esami che fanno media
         * @return float
        */
        public function calcolaMediaPesata() : float {
            $somma = 0;
            $CFU = 0;
            for ($i = 0; $i < count($this->esami); $i++){
                if ($this->esami[$i]->in_avg){
                    $somma += $this->esami[$i]->voto * $this->esami[$i]->cfu;
                    $CFU += $this->esami[$i]->cfu;
                }
            }

            return round($somma/$CFU, 3);
        }

        /**
         * Calcola il numero dei CFU relativi agli esami
         * @return int
         */
        public function restituisciCFU() : int {
            $somma = 0;
            for ($i = 0; $i < count($this->esami); $i++){
                if ($this->esami[$i]->in_cdl)
                    $somma += $this->esami[$i]->cfu;
            }

            return $somma;
        }

        /**
         * Calcola il numero dei CFU relativi agli esami che fanno media
         * @return int
         */
        public function restituisciCFUinAVG() : int {
            $somma = 0;
            for ($i = 0; $i < count($this->esami); $i++){
                if ($this->esami[$i]->in_avg)
                    $somma += $this->esami[$i]->cfu;
            }

            return $somma;
        }
    }

?>