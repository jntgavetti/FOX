<?php
    class Interfaces{
        private $interface;
        private $tipo;
        private $modo;
        private $class;


        public function __get($attr){
            return $this->$attr;
        }

        public function __set($attr, $val){
            $this->$attr = $val;
        }
    }
?>