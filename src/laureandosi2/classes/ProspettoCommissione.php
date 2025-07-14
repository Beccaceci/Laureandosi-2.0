<?php

    namespace classes;

   use setasign\Fpdi\Fpdi;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "ProspettoLaureandoSimulazione.php";

    class ProspettoCommissione extends ProspettoLaureandoSimulazione {
        public array $laureandi;

        public function __construct(array $laureandi, string $dataLaurea) {
            prospettoPDF::__construct($dataLaurea);
            $this->laureandi = $laureandi;
        }

        /**
         * Genera il prospetto per la commissione
         * @param bool $test
         */
        public function genera(bool $test = false) : void {
            $this->pdf->AddPage();
            $this->pdf->SetFont("Arial", "", 12);

            $larghezzaPagina = $this->pdf->GetPageWidth() - 20;

            // Elenco laureandi
            $this->pdf->SetFont("Arial", "", 10);
            $this->pdf->Cell(0, 5, $this->laureandi[0]->cdl->cdl, 0, 1, "C");
            $this->pdf->Ln(5);
            $this->pdf->Cell(0, 5, "LAUREANDOSI 2 - Progettazione: mario.cimino@unipi.it, Amministrazione: rose.rossiello@unipi.it", 0, 1, "C");
            $this->pdf->Ln(5);
            $this->pdf->Cell(0, 5, "LISTA LAUREANDI", 0, 1, "C");

            $this->aggiungiListaLaureandi(5, $larghezzaPagina, 10);

            // Prospetti individuali
            foreach ($this->laureandi as $laureando) {
                parent::__construct($laureando);
                parent::genera($test);
            }

            $this->pdf->Close();
        }

        /**
         * Aggiunge la lista dei laureandi al prospetto destinato alla commissione
         * @param float $altezza
         * @param int $larghezzaTotale
         * @param float $dimFont
        */
        private function aggiungiListaLaureandi(float $altezza, int $larghezzaTotale, float $dimFont) : void {
            $larghezzaDati = $larghezzaTotale / 4;
            $altezzaTitolo = $altezza * 1.5;
            $this->pdf->SetFont("Arial", "", $dimFont);

            $this->pdf->Cell($larghezzaDati, $altezzaTitolo, "COGNOME", 1, 0, "C");
            $this->pdf->Cell($larghezzaDati, $altezzaTitolo, "NOME", 1, 0, "C");
            $this->pdf->Cell($larghezzaDati, $altezzaTitolo, "CDL", 1, 0, "C");
            $this->pdf->Cell($larghezzaDati, $altezzaTitolo, "VOTO LAUREA", 1, 1, "C");

            $this->pdf->SetFont("Arial", "", $dimFont * 0.75);
            foreach ($this->laureandi as $laureando) {
                $this->pdf->Cell($larghezzaDati, $altezza, $laureando->cognome, 1, 0, "C");
                $this->pdf->Cell($larghezzaDati, $altezza, $laureando->nome, 1, 0, "C");
                $this->pdf->Cell($larghezzaDati, $altezza, $laureando->cdl->cdl, 1, 0, "C");
                $this->pdf->Cell($larghezzaDati, $altezza, "/110", 1, 1, "C");
            }
        }

        /**
         * Salva il prospetto destinato alla commissione
         * @param bool $test
         */
        public function salva(bool $test) : void {
            $filename = ($test) ? (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "test_generati") : (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "prospetti_generati");
            $filename .= DIRECTORY_SEPARATOR .  $this->laureandi[0]->cdl->cdl_short . DIRECTORY_SEPARATOR . $this->dataLaurea . DIRECTORY_SEPARATOR . "commissione-" . $this->laureandi[0]->cdl->cdl_short . "-". $this->dataLaurea . ".pdf";
            $this->pdf->Output("F", $filename);
        }
    }

?>