<?php

namespace TrabajoTarjeta;

class TiempoFalso implements TiempoInterface
{
    protected $tiempo;

    protected $feriados = array(
        '01-01', //  Año Nuevo
        '24-03', //  Día Nacional de la Memoria por la Verdad y la Justicia.
        '02-04', //  Día del Veterano y de los Caídos en la Guerra de Malvinas.
        '01-05', //  Día del trabajador.
        '25-05', //  Día de la Revolución de Mayo.
        '17-06', //  Día Paso a la Inmortalidad del General Martín Miguel de Güemes.
        '20-06', //  Día Paso a la Inmortalidad del General Manuel Belgrano. F
        '09-07', //  Día de la Independencia.
        '17-08', //  Paso a la Inmortalidad del Gral. José de San Martín
        '12-10', //  Día del Respeto a la Diversidad Cultural
        '20-11', //  Día de la Soberanía Nacional
        '08-12', //  Inmaculada Concepción de María
        '25-12', //  Navidad
    );
    /**
     * Setea el tiempo con el que queres que comienze la cuenta.
     *
     * @param int $tiempoInicial
     *   Tiempo.
     */
    public function __construct($tiempoInicial = 0)
    {
        $this->tiempo = $tiempoInicial;
    }

    /**
     * Avanza una cantidad de tiempo en segundos.
     *
     * @param int $segundos
     *   La candena de tiempo a avanzar.
     */
    public function avanzar($segundos)
    {
        $this->tiempo += $segundos;
    }

    /**
     * Devuelve el tiempo almacenado.
     *
     * @return int
     *   El tiempo.
     */
    public function time()
    {
        return $this->tiempo;
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
     * Se fija si es feriado, teniendo en cuenta los feridos inamovibles.
     *
     * @return bool
     *   Si es feriado.
     */
    public function esFeriado()
    {
        $fecha = date('d-m', $this->tiempo);

        return in_array($fecha, $this->feriados);
    }
}
