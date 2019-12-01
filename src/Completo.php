<?php
namespace TrabajoTarjeta;

/*/
Tarjeta completo
/*/
class Completo extends Tarjeta
{
    protected $ValorBoleto = 0; //El boleto vale 0

    /**
     * Devuelve el valor de boleto siendo este 0 para la franquicia completa.
     *
     * @param string $linea
     *   La linea de colectivo que no es usada aqui pero si en la clase que extiende.
     *
     * @return int
     *   El valor del boleto.
     */
    public function franquicia()
    {
        return 0; //devuelve 0 si es Completo
    }

    public function restarSaldo($linea)
    {
        return true; //Devuelve el valor ya almacenado
    }
}
