<?php

    namespace classes;

    class Esame{
        public string $nome;
        public int $voto;
        public int $cfu;
        public string $data;
        public bool $in_cdl;
        public bool $in_avg;
        public bool $informatico;

        public function __construct (string $nome, int $voto, int $cfu, string $data, bool $in_cdl, bool $in_avg, bool $informatico) {
            $this->nome =  $nome;
            $this->voto = $voto;
            $this->cfu = $cfu;
            $this->data = $data;
            $this->in_cdl = $in_cdl;
            $this->in_avg = $in_avg;
            $this->informatico = $informatico;
        }
    }

?>