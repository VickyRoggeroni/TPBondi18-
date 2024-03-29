<?php

namespace TrabajoTarjeta;

interface TarjetaInterface
{

    /**
     * Recarga una tarjeta con un cierto valor de dinero.
     *
     * @param float $monto
     *
     * @return bool
     *   Devuelve TRUE si el monto a cargar es válido, o FALSE en caso de que no
     *   sea valido.
     */
    public function recargar(float $monto);

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     */
    public function pagarPlus();
    
    public function obtenerSaldo();

    public function restarSaldo($linea);
    
    public function puedeTransbordo($linea);
    
    public function AlcanzaSaldo();
    
    public function TienePlus();

}
