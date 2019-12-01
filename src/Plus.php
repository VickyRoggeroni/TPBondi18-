<?php

namespace TrabajoTarjeta;

trait Plus
{
    public function TienePlus()
    {   //Si uso menos de dos plus devuelve true
    	
        if($this->plus == 2)
        {
            return false;
        }
        else return true;
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
}