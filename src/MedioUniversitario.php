<?php
namespace TrabajoTarjeta;

class MedioUniversitario extends Medio
{
    protected $DisponiblesDiarios = 0; //Variable que indica la disponibilidad de medios diarios
    protected $ValorBoleto = Precios::medio;

    public function franquicia()
    {
        return 2; //devuelve 2 si es Trasbordo
    }
    
    public function restarSaldo($linea){

        if( Tarjeta::puedeTransbordo($linea) ){
		$this->UltimoValorPagado = Precios::transbordo; //guarda el ultimo valor
		$this->UltimoColectivo = $linea; //guarda el ultimo colectivo
		$this->UltimaHora = $this->tiempo->time(); //guarda la ultima hora
		$this->transbordo = 1; //Marca que el transbordo ya fue usado
		return true;
	}
	elseif(TieneMedioDisponible()){
		$this->UltimoValorPagado = $ValorBoleto;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
        	$this->saldo -= $ValorBoleto;
		return true;
	}
        elseif(AlcanzaSaldo()){
		$this->UltimoValorPagado = Precios::normal;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
        	$this->saldo -= Precios::normal;				//Se resta el boleto
		return true;
	}
	elseif(TienePlus()){
		$this->UltimoValorPagado = Precios::plus;
		$this->UltimoColectivo = $linea;
		$this->UltimaHora = $this->tiempo->time();
		$this->plus++;
		return true;   
	}

        else return false;       //No se pudo restar el saldo
    }

    public function TieneMedioDisponible(){
	$UltimaFecha = date("d/m/y", $this->UltimaHora); //Guarda Cuando fue la ultima utilizacion del boleto
	$ActualFecha = date("d/m/y", $this->tiempo->time()); //Guarda la hora actual
	if ($ActualFecha > $UltimaFecha) {
	    $this->DisponiblesDiarios = 0;
	} //resetea cantidad de medios disponibles por dia
	if ($this->DisponiblesDiarios < 2) { //Si dispone de Medios
	    $this->DisponiblesDiarios++; //Le saca uno
	    return true;
	}
	else return false;
    }
}
