<?php

namespace TrabajoTarjeta;

interface TiempoFalsoInterface
{
    public function avanzar($segundos);
    public function time();
    public function agregarFeriado($dia);
    public function esFeriado();
    public function esDiaDeSemana();    
    public function EsDeNoche();
}
