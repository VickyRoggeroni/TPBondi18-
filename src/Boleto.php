<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface
{

    protected $valor; //Valor pagado

    protected $colectivo; //Colectivo en el que se pago

    protected $tarjeta; //Tarjeta con la que se pago

    protected $cantplus; //Si se pagaron plus en la ultima recarga

    protected $hora; //hora de pago

    protected $idtarjeta; //id de la tarjeta

    protected $boletoCompleto; //Valor de un boleto completo de colectivo para el calculo del plus

    protected $linea; //Linea del colectivo

    protected $saldo; //Saldo restante

    protected $descripcion; //Descripcion del boleto plus

    protected $Tipo; //Tipo de tarjeta que se utilizo

    protected $usoPlus; //Si se utilizaron plus

    protected $PagoPlus;

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
        $this->PagoPlus = "Abona viajes plus " . $this->cantplus * $tarjeta->boletoCompleto() . " y ";
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

    /**
     * Devuelve un string que es la descripcion de el boleto.
     *
     * @return String
     */
    public function obtenerDescripcion()
    {
        $StringAuxiliar = ""; //definimos una variable auxiliar para poder armarla
        if ($this->valor == 0.0) { //si pago 0.0
            if ($this->Tipo == "TrabajoTarjeta\Completo") { //Y la tarjeta es tipo completo, devolvemos que el pago un boleto de tipo completo
                return "Completo 0.0";
            } else { //si no
                if ($this->usoPlus == 1) { //Y si solo uso un plus se muestra "ViajePlus 0.0" o si uso los 2 "UltimoPlus 0.0"
                    $StringAuxiliar = "ViajePlus 0.0";
                } else {
                    $StringAuxiliar = "UltimoPlus 0.0";
                }
            }
        } else { //Si pago algun valor
            switch ($this->valor) { //dependiendo de lo que pago va a ser diferentes textos
                case ($this->boletoCompleto / 2):
                    $StringAuxiliar = "Medio " . ($this->valor);
                    break;
                case (($this->boletoCompleto / 2) * 0.33):
                    $StringAuxiliar = "Trasbordo Medio " . ($this->valor);
                    break;
                case ($this->boletoCompleto * 0.33):
                    $StringAuxiliar = "Trasbordo Normal " . ($this->valor);
                    break;
                case ($this->boletoCompleto):
                    $StringAuxiliar = "Normal " . ($this->valor);
                    break;
            }
        }
        if ($this->cantplus != 0) { // Si pago algun plus une lo que ya formo en el contruct y lo devuelve
            return $this->PagoPlus . $StringAuxiliar;
        }
        return $StringAuxiliar; // si no solo devuelve lo que pago
    }
}
