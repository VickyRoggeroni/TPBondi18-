<?php

namespace TrabajoTarjeta;

interface TiempoInterface
{
    public function avanzar($segundos);
    public function time();
    public function agregarFeriado($dia);
    public function esFeriado();
}