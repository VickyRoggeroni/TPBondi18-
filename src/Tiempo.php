<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface
{
    protected $feriados = array(
        '19-11-18',
        '08-12-18',
        '24-12-18',
        '25-12-18',
        '31-12-18',
        '01-01-19',
        '04-03-19',
        '05-03-19',
        '25-03-19',
        '02-04-19',
        '19-04-19',
        '01-05-19',
        '25-05-19',
        '17-06-19',
        '20-06-19',
        '08-07-19',
        '09-07-19',
        '17-08-19',
        '19-08-19',
        '12-10-19',
        '14-10-19',
        '18-11-19',
        '08-12-19',
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
    public function esFeriado()
    {
        $fecha = date('d-m-y', $this->time());

        return in_array($fecha, $this->feriados);
    }
}
