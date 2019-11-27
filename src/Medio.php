+<?php
namespace TrabajoTarjeta;

/*/
Tarjeta medio
/*/
class Medio extends Tarjeta
{

    protected $UltimaHora = -300; //Para poder usarlo apenas se compra
    protected $ValorBoleto = Precios::medio;
    public function franquicia()
    {
        return 1; //devuelve 1 si es Medio
    }

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
        if (($this->tiempo->time() - $this->UltimaHora) < 299) {
            return false;
        } //Limitacion de 5 minutos
        
        if( puedeTransbordo($linea) ){
		$this->UltimoValorPagado = Precios::transbordo;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
		$this->transbordo = 1;
		return true;
	}
	elseif($this->saldo >= $this->ValorBoleto){
		$this->UltimoValorPagado = $ValorBoleto;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
        	$this->saldo -= $ValorBoleto;
		return true;
	}
	elseif(TienePlus()){
		$this->UltimoValorPagado = Precios::plus;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
		$this->plus++;
		return true;   
	}
        return false; //No se pudo pagar
    }
}
