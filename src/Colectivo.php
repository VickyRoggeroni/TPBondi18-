<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface
{
    protected $linea; //La linea del colectivo

    protected $empresa; //La empresa

    protected $numero; //Numero de interno

    public function __construct($linea, $empresa, $numero)
    {
        $this->linea = $linea; //asignacion de los parametros
        $this->empresa = $empresa;
        $this->numero = $numero;
    }

    /**
     * Devuelve el nombre de la linea. Ejemplo '142 Negro'
     *
     * @return string
     */
    public function linea()
    {
        return $this->linea;
    }

    /**
     * Devuelve el nombre de la empresa. Ejemplo 'Semtur'
     *
     * @return string
     */
    public function empresa()
    {
        return $this->empresa;
    }

    /**
     * Devuelve el numero de unidad. Ejemplo: 12
     *
     * @return int
     */
    public function numero()
    {
        return $this->numero;
    }

    /**
     * Paga un viaje en el colectivo con una tarjeta en particular.
     *
     * @param TarjetaInterface $tarjeta
     *
     * @return BoletoInterface|FALSE
     *  El boleto generado por el pago del viaje. O FALSE si no hay saldo
     *  suficiente en la tarjeta.
     */
    public function pagarCon(TarjetaInterface $tarjeta)
    {
        if (!($tarjeta->restarSaldo($this->linea))) { //Si la funcion para restar el saldo retorna false
            return false; //Falla el pago
        }
        return (new Boleto($this, $tarjeta)); //Crea un boleto con la informacion
    }
}
