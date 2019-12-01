<?php
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
	 /* Comprueba si es el primer viaje realizaxo */
        if($this->UltimoColectivo == null){ //1
            if ($this->AlcanzaSaldo()){ //a
		        $this->TipoBoleto = 4;
		        $this->PagoExitoso = true;
		        $this->saldo -= $this->ValorBoleto;
		        $this->UltimoValorPagado = Precios::medio;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //a
	    
	        if ($this->TienePlus()){ //b
		        $this->TipoBoleto = 2;
		        $this->plus++;
		        $this->PagoExitoso = true;
		        $this->saldo -= Precios::plus;
		        $this->UltimoValorPagado = Precios::plus;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
            	} //b
            	else { //c
		        $this->PagoExitoso = false;
		        $this->TipoBoleto = 3;
		        return $this->PagoExitoso;
	        } //c
        } //1
	   
	 //Si pasaron menos de 5 min es boleto comun
	if(($this->tiempo->time() - $this->UltimaHora) < 299){ //2
	    
		$this->ValorBoleto = Precios::normal; //Cambio para comprobar si le alcanza para un boleto entero
		
	        if ($this->AlcanzaSaldo()){ //a
		        $this->TipoBoleto = 1;
		        $this->PagoExitoso = true;
		        $this->UltimoValorPagado = Precios::normal;
		        $this->saldo -= $this->UltimoValorPagado;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //a
	    
	        if ($this->TienePlus()){ //b
		        $this->TipoBoleto = 2;
		        $this->plus++;
		        $this->PagoExitoso = true;
		        $this->saldo -= Precios::plus;
		        $this->UltimoValorPagado = Precios::plus;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //b
	    
	        else { //c
		        $this->PagoExitoso = false;
		        $this->TipoBoleto = 3;
		        return $this->PagoExitoso;
	        } //c
	} //2
	    
	//No puede marcar otro medio hasta dsp de 5 minutos    
        if(($this->tiempo->time() - $this->UltimaHora) > 299){ //3
		
	    $this->ValorBoleto = Precios::medio;  // Vuelvo al valor medio
		
            if (Transbordo::puedeTransbordo($linea)){ //a
		        $this->TipoBoleto = 0;
		        $this->transbordo = 1;
		        $this->PagoExitoso = true;
		        $this->saldo -= Precios::transbordo;
		        $this->UltimoValorPagado = Precios::transbordo;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //a
	    
	        if ($this->AlcanzaSaldo()){ //b
		        $this->TipoBoleto = 4;
		        $this->PagoExitoso = true;
		        $this->saldo -= $this->ValorBoleto;
		        $this->UltimoValorPagado = Precios::medio;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //b
	    
	        if ($this->TienePlus()){ //c
		        $this->TipoBoleto = 2;
		        $this->plus++;
		        $this->PagoExitoso = true;
		        $this->saldo -= Precios::plus;
		        $this->UltimoValorPagado = Precios::plus;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //c
	    
	        else { //d
		        $this->PagoExitoso = false;
		        $this->TipoBoleto = 3;
		        return $this->PagoExitoso;
	        } //d
        } //3
	  
    } //LLAVE FUNCION
} //Lave de la clase
