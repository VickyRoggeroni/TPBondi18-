<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface
{

    protected $saldo = 0;
    protected $ValorBoleto = Precios::normal;
    protected $plus = 0;
    protected $UltimoValorPagado = null;
    protected $UltimaHora = 0;
    protected $UltimoColectivo;
    protected $pagoplus = 0;
    protected $trasbordo = 0;
    protected $id;
    protected $tiempo;

    public function __construct($id, Tiempo $tiempo)
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

        switch ($monto) { //Diferentes montos a recargar
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
        return true;
    }

    /**
     * Funcion para pagar plus en caso de deberlos.
     */
    public function pagarPlus()
    {
        if ($this->plus == 2) { //Si debe 2 plus
            if ($this->saldo >= ($this->ValorBoleto * 2)) { //Y si le alcanza el saldo para pagarlos
                $this->saldo -= ($this->ValorBoleto * 2); //Se le resta el valor
                $this->plus = 0; //Se le devuelve los plus
                $this->pagoplus = 2; //Se almacena que se pagaron 2 plus
            }
	    else if ($this->saldo >= $this->ValorBoleto) { // Si solo alcanza para 1 plus
                $this->saldo -= $this->ValorBoleto; //se le descuenta
                $this->plus = 1; // Se lo devuelve
                $this->pagoplus = 1; // Se indica que se pago un plus
            }
        } 
	else {
            if ($this->plus == 1 && $this->saldo > $this->ValorBoleto) { //si debe 1 plus
                $this->saldo -= $this->ValorBoleto; //Se le descuenta
                $this->plus = 0; //Se le devuelve
                $this->pagoplus = 1; // Se indica que se pago un plus
            }
        }
    }

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     */
    public function obtenerSaldo()
    {
        return $this->saldo;
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
        if( $this->puedeTransbordo($linea) ){
		$this->UltimoValorPagado = Precios::transbordo;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
		return true;
	}
	elseif($this->AlcanzaSaldo()){
		$this->UltimoValorPagado = $ValorBoleto;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
        	$this->saldo -= $ValorBoleto;	//Se resta el boleto
		return true;
	}
	elseif($this->TienePlus()){
		$this->UltimoValorPagado = Precios::plus;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
		$this->plus++;
		return true;   
	}
	else return false; //si no se pudo restar devuelve falso
    }

    /**
     * Para el caso de la tarjeta ejecuta una funcion que se fija si puede hacer trasbordo.
     *
     * @param string $linea
     *   La linea de colectivo en la que se esta pagando, se utiliza para ver si hizo trasbordo.
     *
     * @return float
     *   El valor del pasaje a pagar.
     */

    /**
     * Funcion para ver si dispone del trasbordo.
     *
     * @param string $linea
     *   La linea de colectivo en la que se esta pagando, se utiliza para ver si hizo trasbordo.
     *
     * @return bool
     *   Si se puede usar transbordo
     */
    protected function puedeTrasbordo($linea)
    {
        if($this->UltimoValorPagado != 0.0 && $this->transbordo == 1){ //Si ya se uso un transbordo, pero despues se volvio a usar la tarjeta
            $this->transbordo = 0; //Se va a resetear el transbordo
        }
	    
	if($this->UltimoColectivo != $linea || $this->UltimoValorPagado != 0.0 || $this->transbordo != 1){   //Se fija condiciones
            if ($this->Tiempo->EsFeriado()){
                return (($this->tiempo->time() - $this->UltimaHora) < 7200);
            }            

            elseif ($this->Tiempo->esDiaDeSemana()){
                if(($this->Tiempo->EsDeNoche()) == false ){//Si no es de noche, es de dia :)
                    return (($this->tiempo->time() - $this->UltimaHora) < 3600);                             //Si paso menos de una hora devuelve true
                }
                if($this->Tiempo->EsDeNoche()){
                    return (($this->tiempo->time() - $this->UltimaHora) < 7200);
                }
            }

            else return(($this->tiempo->time() - $this->UltimaHora) < 7200);                                 //si no es feriado o dia de semana, es finde, entonces son dos horas el transb
        }

        else return false;                                                                                  //Si no cumple las condiciones devuelve false 
    }

    private function AlcanzaSaldo(){                                //Si el saldo es mayor o igual al valor del boleto, entonces le alcanza para pagarlo
    	return ($this->saldo >= $this->ValorBoleto);
    }
	
    private function TienePlus(){                                   //Si uso menos de dos plus devuelve true
    	return ($this->plus < 2);
    }

    /**
     * Setea a 0 el "pago plus". Esta funcion se ejecutara cuando se emite el boleto.
     *
     * @return int
     *   La cantidad de plus que pago en la ultiima recarga.
     */
    public function obtenerPagoPlus()
    {
        $pagoplusaux = $this->pagoplus; // Se almacena en un auxiliar
        $this->pagoplus = 0; // se Reinicia
        return $pagoplusaux; // Se devuelve el auxiliar
    }

    /**
     * Devuelve el valor completo del boleto.
     *
     * @return float
     */
    public function boletoCompleto()
    {
        return $this->ValorBoleto; // Devuelve el valor de un boleto completo
    }

    /**
     * Devuelve el ID de la tarjeta.
     *
     * @return int
     */
    public function obtenerId()
    {
        return $this->id; //Devuelve el id de la tarjeta
    }

    /**
     * Devuelve el ultimo valor pagado.
     *
     * @return float
     */
    public function valorPagado()
    {
        return $this->UltimoValorPagado; // Devuelve el ultimo valor que se pago
    }

    /**
     * Devuelve la ultima hora en la que se uso la tarjeta.
     *
     * @return int
     */
    public function ultimaHoraUsada()
    {
        return $this->UltimaHora; // Devuelve la ultima hora a la que se pago
    }

    /**
     * Devuelve si se utilizo un viaje plus.
     *
     * @return int
     */
    public function usoPlus()
    {
        return $this->plus; // Devuelve si se utilizo un viaje plus
    }
}
