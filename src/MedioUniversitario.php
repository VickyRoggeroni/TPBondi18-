<?php
namespace TrabajoTarjeta;

class MedioUniversitario extends Medio
{
    protected $DisponiblesDiarios = 0; //Variable que indica la disponibilidad de medios diarios
    protected $ValorBoleto = Precios::medio;

    public function franquicia()
    {
        return 2; //devuelve 2 si es Medio Universitario
    }
    
    public function restarSaldo($linea){

        if($this->UltimoColectivo == null){ //1
            if ($this->AlcanzaSaldo() && $this->TieneMedioDisponible()){ //a
		        $this->TipoBoleto = 5;
		        $this->DisponiblesDiarios++;
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
	    
	if(($this->tiempo->time() - $this->UltimaHora) < 299){ //2     //Si pasaron menos de 5 min es boleto comun
	    
	        $this->ValorBoleto = Precios::normal; 			//Cambia Para ver si le alcanza para uno entero
		
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
        if(($this->tiempo->time() - $this->UltimaHora) > 299){ //3     //Si pasaron mas puedo volver a usar el medio
	   
            $this->ValorBoleto = Precios::medio;
		
            if ($this->puedeTransbordo($linea)){ //a
		        $this->TipoBoleto = 0;
		        $this->transbordo = 1;
		        $this->PagoExitoso = true;
		        $this->saldo -= Precios::transbordo;
		        $this->UltimoValorPagado = Precios::transbordo;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //a
	    
	        if ($this->AlcanzaSaldo() && $this->TieneMedioDisponible()){ //b
		        $this->TipoBoleto = 5;
		        $this->DisponiblesDiarios++;
			$this->PagoExitoso = true;
		        $this->saldo -= $this->ValorBoleto;
		        $this->UltimoValorPagado = Precios::medio;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //b
		
		$this->ValorBoleto = Precios::normal;
		
		//Si no tiene medios dispnible, tiene que ver si le alcanza para uno entero
		if ($this->AlcanzaSaldo()){ //c
		        $this->TipoBoleto = 1;
		        $this->PagoExitoso = true;
		        $this->saldo -= $this->ValorBoleto;
		        $this->UltimoValorPagado = Precios::normal;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //c
	    
	        if ($this->TienePlus()){ //d
		        $this->TipoBoleto = 2;
		        $this->plus++;
		        $this->PagoExitoso = true;
		        $this->saldo -= Precios::plus;
		        $this->UltimoValorPagado = Precios::plus;
		        $this->UltimoColectivo = $linea;
		        $this->UltimaHora = $this->tiempo->time();
		        return $this->PagoExitoso;
	        } //d
	    
	        else { //e
		        $this->PagoExitoso = false;
		        $this->TipoBoleto = 3;
		        return $this->PagoExitoso;
	        } //e
        } //3
    }

    public function TieneMedioDisponible(){
	$UltimaFecha = date("d/m/y", $this->UltimaHora); //Guarda Cuando fue la ultima utilizacion del boleto
	$ActualFecha = date("d/m/y", $this->tiempo->time()); //Guarda la hora actual
	
	if ($ActualFecha > $UltimaFecha) {
	    $this->DisponiblesDiarios = 0;
	} //resetea cantidad de medios disponibles por dia
	    
    	if($this->DisponiblesDiarios == 2){
		return false;
	}
	else return true;
    }
}
