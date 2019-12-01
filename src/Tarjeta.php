<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface
{
    use ConsultasTarjeta;
    use Transbordo;
    use Plus;

    protected $saldo = 0;
    protected $ValorBoleto = Precios::normal;
    protected $plus = 0;
    protected $UltimoValorPagado = null;
    protected $UltimaHora = 0;
    protected $UltimoColectivo = null;
    protected $pagoplus = 0;
    protected $transbordo = 0;
    protected $id;
    protected $tiempo;
    protected $TipoBoleto = 0; //transbordo = 0, normal = 1, plus = 2, invalido = 3, medio = 4, medioU = 5
    public $PagoExitoso = false;

    public function __construct($id, $tiempo)
    {
        $this->id = $id; //Guarda el ID
        $this->tiempo = $tiempo; //Guarda la variable tiempo la cual le es inyectada
    }

    /**
     * Funcion para recargar la tarjeta.
     *
     * @param float $monto
     *   Las cargas aceptadas de tarjetas son: (10, 20, 30, 50, 100, 1119.90 y 2114.11)
     *   Cuando se cargan $1119,9 se acreditan de forma adicional: 180,10
     *   Cuando se cargan $2114,11 se acreditan de forma adicional: 485,89
     *
     * @return bool
     *   Si fue posible realizar la carga.
     */
    public function recargar(float $monto)
    {
        switch ($monto) 
        { //Diferentes montos a recargar
            case 10:
                $this->saldo += 10;
                break;
            case 20:
                $this->saldo += 20;
                break;
            case 30:
                $this->saldo += 30;
                break;
            case 50:
                $this->saldo += 50;
                break;
            case 100:
                $this->saldo += 100;
                break;
            case 1119.90:
                $this->saldo += 1300.00;
                break;
            case 2114.11:
                $this->saldo += 2600.00;
                break;
            default:
                //Devuelve false si el monto ingresado no es válido
                return false;
        }

        $this->pagarPlus(); //Ejecuta la funcion parta pagar plus en caso de que los deba
        // Devuelve true si el monto ingresado es válido
	    $this->TipoBoleto = 0;
        return true;
    }

    /**
     * Resta un boleto a la tarjeta.
     *
     * @param string $linea
     *   La linea de colectivo en la que se esta pagando, se utiliza para ver si hizo trasbordo.
     *
     * @return bool
     *   Si fue posible realizar el pago.
     */
    public function restarSaldo($linea)
    {
        if ($this->puedeTransbordo($linea))
        {
            $this->TipoBoleto = 0;
            $this->transbordo = 1;
            $this->PagoExitoso = true;
            $this->saldo -= Precios::transbordo;
            $this->UltimoValorPagado = Precios::transbordo;
            $this->UltimoColectivo = $linea;
            $this->UltimaHora = $this->tiempo->time();
            return $this->PagoExitoso;
        }
	    
        if ($this->AlcanzaSaldo())
        {
            $this->TipoBoleto = 1;
            $this->PagoExitoso = true;
            $this->saldo -= $this->ValorBoleto;
            $this->UltimoValorPagado = Precios::normal;
            $this->UltimoColectivo = $linea;
            $this->UltimaHora = $this->tiempo->time();
            return $this->PagoExitoso;
        }
	    
        if ($this->TienePlus())
        {
            $this->TipoBoleto = 2;
            $this->plus++;
            $this->PagoExitoso = true;
            $this->saldo -= Precios::plus;
            $this->UltimoValorPagado = Precios::plus;
            $this->UltimoColectivo = $linea;
            $this->UltimaHora = $this->tiempo->time();
            return $this->PagoExitoso;
        }
        else 
        {
            $this->PagoExitoso = false;
            $this->TipoBoleto = 3;
            return $this->PagoExitoso;
        }
    }
}