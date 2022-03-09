<?php
    class Iface{
        private $iface;
        private $tipo;
        private $funcao;
        private $mac;
        private $addr;
        private $stat;
        private $int_pai;


        public function __get($attr){
            return $this->$attr;
        }

        public function __set($attr, $val){
            $this->$attr = $val;
        }
    }
?>