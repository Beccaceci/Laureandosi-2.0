<?php

    namespace classes;

    use setasign\Fpdi\Fpdi;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "ProspettoLaureando.php";

    class ProspettoLaureandoSimulazione extends ProspettoLaureando {
        public function __construct(Laureando $laureando) {
            parent::__construct($laureando);
        }

        /**
         * Genera il prospetto del laureando con simulazione del voto di laurea
         * @param bool $test
         * @return void
         */
        public function genera(bool $test = false) : void {
            $this->aggiungiProspettoLaureando($test);
            $this->aggiungiSimulazione();
        }

        /**
         * Aggiunge il prospetto del laureando
         * @param bool $test
         * @return void
         */
        private function aggiungiProspettoLaureando(bool $test) : void {
            //genero il prospetto del laureando
            parent::genera();
            parent::salva($test);

            $filename = ($test) ? (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "test_generati") : (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "prospetti_generati");
            $filename .= DIRECTORY_SEPARATOR . $this->laureando->cdl->cdl_short . DIRECTORY_SEPARATOR . $this->dataLaurea . DIRECTORY_SEPARATOR . $this->laureando->matricola . ".pdf";
            
            $this->pdf->SetFontSize(10);
            //IMPORTANTE: importo il prospetto del laureando senza crearne una nuova copia
            $pageCount = $this->pdf->setSourceFile($filename);
            for ($i = 1; $i <= $pageCount; $i++) { //prendo in considerazione anche il caso in cui il prospetto del laureando sia distribuito su più pagine
                $templateId = $this->pdf->importPage($i);
                $size = $this->pdf->getTemplateSize($templateId);
                
                $this->pdf->SetAutoPageBreak(false);
                $this->pdf->AddPage($size["orientation"], [$size["width"], $size["height"]]);
                $this->pdf->useTemplate($templateId, 0, 0, $size["width"], $size["height"]);
            }
            $this->pdf->SetY($this->pdfLaureando->GetY());
        }

        /**
         * Aggiunge la simulazione del voto di laurea
         * @return void
         */
        private function aggiungiSimulazione() : void {            
            $this->pdf->SetFontSize(10);
            $this->pdf->Ln(3);
            $this->pdf->Cell(0, 5, "SIMULAZIONE DI VOTO DI LAUREA", 1, 1, "C");

            $par_t = $this->laureando->cdl->par_T;
            $par_c = $this->laureando->cdl->par_C;
            list($t_min, $t_max, $t_step) = array_values($par_t);
            list($c_min, $c_max, $c_step) = array_values($par_c);

            $formula = $this->laureando->cdl->voto_laurea;

            $formula = str_replace(array("M", "CFU"), array($this->laureando->calcolaMediaPesata(), $this->laureando->restituisciCFU()), $formula);

            $parametro = "";
            $colonne = null;
            $righe = null;
            $width_col = null;

            $i_min = null;
            $i_max = null;
            $i_step = null;

            $informazioni_calcolo = null;

            if ($t_min) {
                $formula = str_replace("C", 0, $formula);
                $parametro = "T";

                $colonne = ($t_max - $t_min) / $t_step;
                $colonne = ($colonne > 7) ? 2 : 1;
                $righe = ceil(($t_max - $t_min + 1) / $t_step / $colonne);
                $width_col = ($this->pdf->GetPageWidth() - 20) / $colonne;

                for ($i = 0; $i < $colonne; $i++) {
                    $this->pdf->Cell($width_col / 2, 5, "VOTO TESI (T)", 1, 0, "C");
                    $this->pdf->Cell($width_col / 2, 5, "VOTO DI LAUREA", 1, 0, "C");
                }
                $this->pdf->Ln();

                $i_min = $t_min;
                $i_max = $t_max;
                $i_step = $t_step;

                $informazioni_calcolo = $this->laureando->cdl->msg_commissione;
            }
            elseif ($c_min) {
                $formula = str_replace("T", 0, $formula);
                $parametro = "C";

                $colonne = ($c_max - $c_min) / $c_step;
                $colonne = ($colonne > 7) ? 2 : 1;
                $righe = ceil(($c_max - $c_min + 1) / $c_step / $colonne);
                $width_col = ($this->pdf->GetPageWidth() - 20) / $colonne;

                for ($i = 0; $i < $colonne; $i++) {
                    $this->pdf->Cell($width_col / 2, 5, "VOTO COMMISSIONE (C)", 1, 0, "C");
                    $this->pdf->Cell($width_col / 2, 5, "VOTO DI LAUREA", 1, 0, "C");
                }
                $this->pdf->Ln();

                $i_min = $c_min;
                $i_max = $c_max;
                $i_step = $c_step;

                if (is_a($this->laureando, LaureandoInformatica::class))
                    $informazioni_calcolo = "scegli voto commissione, prendi il corrispondente voto di laurea " . "e somma il voto di tesi tra 1 e 3, quindi arrotonda";
                else
                    $informazioni_calcolo = "scegli voto commissione, prendi il corrispondente voto di laurea ed arrotonda";
            }

            $y_cord = $this->pdf->GetY();
            for ($i = $i_min, $col = 0; $col < $colonne && $i <= $i_max; $col++) {
                $this->pdf->SetY($y_cord);
                for ($j = 0; $j < $righe && $i <= $i_max; $j++, $i += $i_step) {
                    $this->pdf->SetX(10 + $col * $width_col);
                    $val = round(eval("return " . str_replace($parametro, $i, $formula) . ";"), 3);

                    $this->pdf->Cell($width_col / 2, 5, $i, 1, 0, "C");
                    $this->pdf->Cell($width_col / 2, 5, $val, 1, 1, "C");
                }
            }
            $this->pdf->SetY($y_cord + $righe * 5);
            $this->pdf->Ln(4);

            $this->pdf->MultiCell(0, 5, "VOTO DI LAUREA FINALE: " . $informazioni_calcolo);
        }

        public function salva (bool $test) : void {
        }
    }

?>