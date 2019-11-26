<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface
{
    protected $feriados = array(
        '18-11-19',
        '08-12-19',
        '24-12-19',
        '25-12-19',
        '31-12-19',
        '01-01-20',
        '24-02-20',
        '25-02-20',
        '23-03-20',
        '24-03-20',
        '02-04-20',
        '10-04-20',
        '01-05-20',
        '25-05-20',
        '15-06-20',
        '20-06-20',
        '08-07-19',
        '09-07-20',
        '10-07-20',
        '17-08-20',
        '12-10-20',
        '23-11-20',
        '07-12-20',
        '08-12-20',
        '25-12-20',
    );

    /**
     * Devuelve el tiempo real
     *
     * @return int
     */
    public function time()
    {
        return time();
    }

    /**
     * Agrega a la lista de feriados uno que se pase como parametro.
     *
     * @param string $dia
     */
    public function agregarFeriado($dia)
    {
        array_push($this->feriados, $dia);
    }

    /**
     * Se fija si el dia en el que es ejecutada la funcion es feriado tienendo encuanta todos los feriados hasta el 2019
     *
     * @return bool
     */
    public function EsFeriado()
    {
        $fecha = date('d-m-y', $this->time());

        return in_array($fecha, $this->feriados);
    }
    
    public function esDiaDeSemana(){
        return (date() != sabado || date() != domingo);
    }

    public function EsDeNoche(){
        return (tiempo >= 22hr || tiempo <= 6hr)            //Entre las 10 de la noche y las 6 de la mañana
    }
}
