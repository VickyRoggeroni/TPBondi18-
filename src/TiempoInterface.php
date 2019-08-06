<?php

namespace TrabajoTarjeta;

interface TiempoInterface
{

    public function time();

    public function agregarFeriado($dia);

    public function esFeriado();

}
