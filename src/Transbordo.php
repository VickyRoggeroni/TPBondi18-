<?php

namespace TrabajoTarjeta;

trait Transbordo
{
    /**
     * Funcion para ver si dispone del trasbordo.
     *
     * @param string $linea
     *   La linea de colectivo en la que se esta pagando, se utiliza para ver si hizo trasbordo.
     *
     * @return bool
     *   Si se puede usar transbordo
     */
    public function puedeTransbordo($linea)
    {
        if ($this->UltimoColectivo == null || $this->TipoBoleto == 2 || $this->UltimoColectivo == $linea)
        {
		return false;
        }
	    
        if($this->TipoBoleto != 2 && $this->transbordo == 1)
        { //Si ya se uso un transbordo, pero despues se volvio a usar la tarjeta
                $this->transbordo = 0; //Se va a resetear el transbordo
        }
	    
        if($this->UltimoColectivo != $linea || $this->TipoBoleto != 2 || $this->transbordo != 1)
        {   //Se fija condiciones
            if ($this->tiempo->EsFeriado())
            {
                return (($this->tiempo->time() - $this->UltimaHora) < 7200);
            }            
            elseif ($this->tiempo->esDiaDeSemana())
            {
                if(($this->tiempo->EsDeNoche()) == false )
                {//Si no es de noche, es de dia :)
                    return (($this->tiempo->time() - $this->UltimaHora) < 3600);                             //Si paso menos de una hora devuelve true
                }
                if($this->tiempo->EsDeNoche())
                {
                    return (($this->tiempo->time() - $this->UltimaHora) < 7200);
                }
            }
            else return(($this->tiempo->time() - $this->UltimaHora) < 7200);                                 //si no es feriado o dia de semana, es finde, entonces son dos horas el transb
        }
        else return false;                                                                                  //Si no cumple las condiciones devuelve false 
    }
}