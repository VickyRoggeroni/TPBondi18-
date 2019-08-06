<?php
namespace TrabajoTarjeta;

class MedioUniversitario extends Medio
{

    protected $DisponiblesDiarios = 0; //Variable que indica la disponibilidad de medios diarios

    /**
     * Devuelve el valor del boleto a pagar, pero antes se fija si puede hacer un trasbordo utilizando al otra funcion y si es uno de los 2 medios diarios que dispone.
     *
     * @param string $linea
     *   La linea en la que esta intentando pagar.
     *
     * @return float
     *   El valor del boleto a pagar.
     */
    protected function calculaValor($linea)
    {
        $BoletoTemporal = 0;
        $UltimaFecha = date("d/m/y", $this->UltimaHora); //Guarda Cuando fue la ultima utilizacion del boleto
        $ActualFecha = date("d/m/y", $this->tiempo->time()); //Guarda la hora actual
        if ($ActualFecha > $UltimaFecha) {$this->DisponiblesDiarios = 0;} // Si cambio de dia entre la utilizacion anterior se reinicia la disponibilidad
        if ($this->DisponiblesDiarios < 2) { //Si dispone de Medios
            $this->DisponiblesDiarios++; //Le saca uno
            $BoletoTemporal = (($this->ValorBoleto) / 2); //Y devualve la mitad del valor
        } else {$BoletoTemporal = $this->ValorBoleto;} // Devuelve el valor entero

        return ($this->puedeTrasbordo($linea,$BoletoTemporal));
    }
}
