<?php
namespace TrabajoTarjeta;

/*/
Tarjeta medio
/*/
class Medio extends Tarjeta
{

    protected $UltimaHora = -300; //Para poder usarlo apenas se compra

    /**
     * Resta el saldo a la tarjeta, pero con una limitacion de no poder pagar un boleto si pasaron menos de 5 minutos.
     *
     * @param string $linea
     *   La linea en la que esta intentando pagar.
     *
     * @return bool
     *   Si se pudo realizar el pago.
     */
    public function restarSaldo($linea)
    {
        if (($this->tiempo->time() - $this->UltimaHora) < 299) {return false;} //Limitacion de 5 minutos
        $ValorARestar = $this->calculaValor($linea); //Llama a la funcion que calcula el valor del boleto a pagar
        if ($this->saldo >= $ValorARestar) { //Se fija si le alcanza el saldo
            $this->saldo -= $ValorARestar; //En caso de alcanzar lo resta
            $this->UltimoValorPagado = $ValorARestar; //Guarda el valor del ultimo pago que se realizo
            $this->UltimoColectivo = $linea;
            $this->UltimaHora = $this->tiempo->time(); //Guarda la hora de este pago
            return true; //se completa el pago
        }
        if ($this->plus < 2) { //En caso de no alcanzarle el saldo, se fija si dispone de plus
            $this->plus++; //le saca un plus
            $this->UltimaHora = $this->tiempo->time(); //Guarda la hora de utilizacion del plus
            $this->UltimoColectivo = $linea;
            $this->UltimoValorPagado = 0.0; //Indica que se pago 0.0
            return true; //Completa el pago
        }
        return false; //No Se pudo pagar
    }

    /**
     * Devuelve el valor del boleto a pagar, pero antes se fija si puede hacer un trasbordo utilizando al otra funcion.
     *
     * @param string $linea
     *   La linea en la que esta intentando pagar.
     *
     * @return float
     *   El valor del boleto a pagar.
     */
    protected function calculaValor($linea)
    {
        return ($this->puedeTrasbordo($linea, ($this->ValorBoleto / 2)));
    }
}
