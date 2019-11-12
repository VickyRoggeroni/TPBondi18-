<?php

namespace TrabajoTarjeta;

class Trasbordo extends Tarjeta
{
    protected $ValorBoleto = 0;

    public function franquicia()
    {
        return 3; //devuelve 3 si es Trasbordo
    }
}