<?php

    namespace classes;

    use FPDF;
    use setasign\Fpdi\Fpdi;

    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "fpdf184" . DIRECTORY_SEPARATOR . "fpdf.php";    
    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "fpdi" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "autoload.php";

    abstract class ProspettoPDF {
        protected Fpdi $pdf;
        protected string $dataLaurea;

        protected function __construct(string $dataLaurea) {
            $this->pdf = new Fpdi();
            $this->dataLaurea = $dataLaurea;
        }

        abstract public function genera(bool $test) : void;

        abstract public function salva(bool $test) : void;
    }

?>
