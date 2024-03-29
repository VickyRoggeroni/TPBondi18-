<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface
{

    protected $valor;                   //Valor pagado
    protected $colectivo;               //Colectivo en el que se pago
    protected $tarjeta;                 //Tarjeta con la que se pago
    protected $cantplus;                //Si se pagaron plus en la ultima recarga
    protected $hora;                    //hora de pago
    protected $idtarjeta;               //id de la tarjeta
    protected $boletoCompleto;           //Valor de un boleto completo de colectivo para el calculo del plus
    protected $linea;                   //Linea del colectivo
    protected $saldo;                   //Saldo restante
    protected $Tipo;                    //Tipo de tarjeta que se utilizo
    protected $usoPlus;                 //Si se utilizaron plus              //Pa que carajo quiero esto si ya esta el cantPlus.

    public function __construct($colectivo, $tarjeta)
    {
        $this->valor = ($tarjeta->valorPagado());
        $this->colectivo = $colectivo;
        $this->tarjeta = $tarjeta;
        $this->usoPlus = $tarjeta->usoPlus();
        $this->cantplus = $tarjeta->obtenerPagoPlus();
        $this->hora = date("d/m/Y H:i:s", $tarjeta->ultimaHoraUsada());
        $this->idtarjeta = $tarjeta->obtenerId();
        $this->boletoCompleto = $tarjeta->boletoCompleto();
        $this->linea = $colectivo->linea();
        $this->saldo = $tarjeta->obtenerSaldo();
        $this->Tipo = get_class($tarjeta);
    }

    /**
     * Devuelve el valor del boleto.
     *
     * @return int
     */
    public function obtenerValor()
    {
        return $this->valor;
    }

    public function obtenerCantPlus()
    {
        return $this->cantplus;
    }

    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     *
     * @return ColectivoInterface
     */
    public function obtenerColectivo()
    {
        return $this->colectivo;
    }

    /**
     * Devuelve la hora a la que se pago el boleto.
     *
     * @return String
     */
    public function obtenerFecha()
    {
        return $this->hora;
    }

    /**
     * Devuelve un objeto que respresenta la tarjeta con la cual se pagó.
     *
     * @return TarjetaInterface
     */
    public function obtenerTarjeta()
    {
        return $this->tarjeta;
    }

    /**
     * Devuelve la linea del colectivo.
     *
     * @return String
     */
    public function obtenerLinea()
    {
        return $this->linea;
    }

    /**
     * Devuelve un objeto que respresenta el total abonado.
     *
     * @return Int
     */
    public function obtenerAbonado()
    {
        $TotalAbonado = $this->obtenerValor() + ($this->boletoCompleto * $this->cantplus);
        return $TotalAbonado;
    }

    /**
     * Devuelve un numero que respresenta el ID de la tarjeta con la cual se pagó.
     *
     * @return Int
     */
    public function obtenerIdTarjeta()
    {
        return $this->idtarjeta;
    }

    /**
     * Devuelve un numero que respresenta el saldo restante de la tarjeta.
     *
     * @return Float
     */
    public function obtenerSaldo()
    {
        return $this->saldo;
    }

    /**
     * Devuelve un string que respresenta el tipo de tarjeta.
     *
     * @return String
     */
    public function obtenerTipo()
    {
        return $this->Tipo;
    }
}