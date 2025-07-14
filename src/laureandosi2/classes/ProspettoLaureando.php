<?php

    namespace classes;

    use setasign\Fpdi\Fpdi;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "ProspettoPDF.php";
    
    class ProspettoLaureando extends ProspettoPDF {
        protected Laureando $laureando;
        private bool $informatico;
        protected Fpdi $pdfLaureando;

        public function __construct(Laureando $laureando) {
            $this->pdfLaureando = new Fpdi();            
            $this->laureando = $laureando;
            $this->informatico = is_a($this->laureando, LaureandoInformatica::class);
        }

        /**
         * Genera il prospetto per il laureando specificato
         * @param bool $test
         */
        public function genera(bool $test = false) : void {
            $this->pdfLaureando->SetFont("Arial", "", 12);
            $this->pdfLaureando->AddPage();

            $cdl = (defined("TEST")) ? "TEST" : $this->laureando->cdl->cdl;
            $this->pdfLaureando->Cell(0, 5, $cdl, 0, 1, "C");
            $this->pdfLaureando->Cell(0, 5, "CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA", 0, 1, "C");

            $this->aggiungiDatiAnagrafici();
            $this->aggiungiCarriera();
            $this->aggiungiParametriCalcolati();
        }

        /**
         * Aggiunge i dati anagrafici del laureando
         * @return void
         */
        private function aggiungiDatiAnagrafici() : void {
            $this->pdfLaureando->SetFontSize(10);
            $this->pdfLaureando->Rect($this->pdfLaureando->GetX(), $this->pdfLaureando->GetY(), ($this->pdfLaureando->GetPageWidth() - 20), (5 * (5 + $this->informatico)));
            $this->pdfLaureando->Cell(60, 5, "Matricola:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->matricola, 0, 1);
            $this->pdfLaureando->Cell(60, 5, "Nome:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->nome, 0, 1);
            $this->pdfLaureando->Cell(60, 5, "Cognome:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->cognome, 0, 1);
            $this->pdfLaureando->Cell(60, 5, "Email:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->email, 0, 1);
            $this->pdfLaureando->Cell(60, 5, "Data:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->dataLaurea, 0, 1);

            if ($this->informatico) {
                $this->pdfLaureando->Cell(60, 5, "BONUS:", 0, 0);
                $this->pdfLaureando->Cell(0, 5, $this->laureando->bonus ? "SI" : "NO", 0, 1);
            }

            $this->pdfLaureando->Ln(1.5);
        }

        /**
         * Aggiunge la carriera del laureando
         * @return void
         */
        private function aggiungiCarriera() : void {
            $this->pdfLaureando->SetFontSize(10);

            $this->pdfLaureando->Cell(($this->pdfLaureando->GetPageWidth() - 10 * (5 + $this->informatico)), 5, "ESAME", 1, 0, "C");
            $this->pdfLaureando->Cell(10, 5, "CFU", 1, 0, "C");
            $this->pdfLaureando->Cell(10, 5, "VOT", 1, 0, "C");
            $this->pdfLaureando->Cell(10, 5, "MED", 1, 0, "C");
            if ($this->informatico)
                $this->pdfLaureando->Cell(10, 5, "INF", 1, 0, "C");
            $this->pdfLaureando->Ln();

            $this->pdfLaureando->SetFontSize(8);
            
            foreach ($this->laureando->esami as $esame){
                if ($esame->in_cdl){
                    $this->pdfLaureando->Cell(($this->pdfLaureando->GetPageWidth()  - 10 * (5 + $this->informatico)), 4, $esame->nome, 1, 0);
                    $this->pdfLaureando->Cell(10, 4, $esame->cfu, 1, 0, "C");
                    $this->pdfLaureando->Cell(10, 4, $esame->voto, 1, 0, "C");
                    $this->pdfLaureando->Cell(10, 4, $esame->in_avg ? "X" : "", 1, 0, "C");
                    if ($this->informatico)
                        $this->pdfLaureando->Cell(10, 4, $esame->informatico ? "X" : "", 1, 0, "C");
                    $this->pdfLaureando->Ln();
                }
            }

            $this->pdfLaureando->Ln(3.5);
        }

        /**
         * Aggiunge i parametri calcolati relativi al laureando
         * @return void
         */
        private function aggiungiParametriCalcolati() : void {
            $this->pdfLaureando->SetFontSize(10);

            $this->pdfLaureando->Rect($this->pdfLaureando->GetX(), $this->pdfLaureando->GetY(), ($this->pdfLaureando->GetPageWidth() - 20), 20 + 10 * $this->informatico);

            $this->pdfLaureando->Cell(80, 5, "Media Pesata (M):", 0, 0);
            $this->pdfLaureando->Cell(0, 5, round($this->laureando->calcolaMediaPesata(), 3), 0, 1);
            $this->pdfLaureando->Cell(80, 5, "Crediti che fanno media (CFU):", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->restituisciCFUinAVG(), 0, 1);
            $this->pdfLaureando->Cell(80, 5, "Crediti curriculari conseguiti:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->restituisciCFU() . "/" . $this->laureando->cdl->TOT_CFU, 0, 1);
            
            if ($this->informatico) {
                $this->pdfLaureando->Cell(80, 5, "Voto di tesi (T):", 0, 0);
                $this->pdfLaureando->Cell(0, 5, 0, 0, 1);
            }

            $this->pdfLaureando->Cell(80, 5, "Formula calcolo voto di laurea:", 0, 0);
            $this->pdfLaureando->Cell(0, 5, $this->laureando->cdl->voto_laurea, 0, 1);
            
            if ($this->informatico) {
                $this->pdfLaureando->Cell(80, 5, "Media pesata esami INF:", 0, 0);
                $this->pdfLaureando->Cell(0, 5, $this->laureando->calcolaMediaInformatica(), 0, 1);
            }
        }

        /**
         * Salva il prospetto generato
         * @param bool $test
        */
        public function salva (bool $test) : void {
            if ($test)
                $filename = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "test_generati";
            else
                $filename = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "prospetti_generati";
            $filename .= DIRECTORY_SEPARATOR . $this->laureando->cdl->cdl_short . DIRECTORY_SEPARATOR . $this->dataLaurea . DIRECTORY_SEPARATOR . $this->laureando->matricola . ".pdf";
            $this->pdfLaureando->Output("F", $filename);
        }
    }

?>