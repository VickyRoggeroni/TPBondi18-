<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface
{

    protected $saldo = 0;

    protected $ValorBoleto = 14.8;

    protected $plus = 0;

    protected $UltimoValorPagado = null;

    protected $UltimaHora = 0;

    protected $UltimoColectivo;

    protected $pagoplus = 0;

    protected $Ultimotrasbordo = 1;

    protected $id;

    protected $tiempo;

    public function __construct($id, TiempoInterface $tiempo)
    {
        $this->id = $id; //Guarda el ID
        $this->tiempo = $tiempo; //Guarda la variable tiempo la cual le es inyectada
    }

    /**
     * Funcion para recargar la tarjeta.
     *
     * @param float $monto
     *   Las cargas aceptadas de tarjetas son: (10, 20, 30, 50, 100, 510,15 y 962,59)
     *   Cuando se cargan $510,15 se acreditan de forma adicional: 81,93
     *   Cuando se cargan $962,59 se acreditan de forma adicional: 221,58
     *
     * @return bool
     *   Si fue posible realizar la carga.
     */
    public function recargar($monto)
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
            case 510.15:
                $this->saldo += 592.08;
                break;
            case 962.59:
                $this->saldo += 1184.17;
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
    protected function pagarPlus()
    {
        if ($this->plus == 2) { //Si debe 2 plus
            if ($this->saldo >= ($this->ValorBoleto * 2)) { //Y si le alcanza el saldo para pagarlos
                $this->saldo -= ($this->ValorBoleto * 2); //Se le resta el valor
                $this->plus = 0; //Se le devuelve los plus
                $this->pagoplus = 2; //Se almacena que se pagaron 2 plus
            } else if ($this->saldo >= $this->ValorBoleto) { // Si solo alcanza para 1 plus
                $this->saldo -= $this->ValorBoleto; //se le descuenta
                $this->plus = 1; // Se lo devuelve
                $this->pagoplus = 1; // Se indica que se pago un plus
            }
        } else {
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
        $ValorARestar = $this->calculaValor($linea); //Calcula el valor de el boleto
        if ($this->saldo >= $ValorARestar) { // Si hay saldo
            $this->saldo -= $ValorARestar; //Se le resta
            $this->UltimoValorPagado = $ValorARestar; //Se guarda cuanto pago
            $this->UltimoColectivo = $linea;
            $this->UltimaHora = $this->tiempo->time(); //Se guarda la hora de la transaccion
            return true; //Se finaliza la funcion
        }
        if ($this->plus < 2) { //Si tiene plus disponibles
            $this->plus++; // Se le resta
            $this->UltimoValorPagado = 0.0; //Se indica que se pago 0.0
            $this->UltimoColectivo = $linea;
            $this->UltimaHora = $this->tiempo->time(); //Se almacena la hora de la transaccion
            return true; // Se finaliza
        }
        return false; // No fue posible pagar
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
    protected function calculaValor($linea)
    {
        return ($this->puedeTrasbordo($linea, $this->ValorBoleto));
    }

    /**
     * Funcion para ver si dispone del trasbordo.
     *
     * @param string $linea
     *   La linea de colectivo en la que se esta pagando, se utiliza para ver si hizo trasbordo.
     *
     * @param float $ValorBoleto
     *   El valor del boleto al que se realiza un 33%.
     *
     * @return float
     *   Si fue posible realizar la carga.
     */
    protected function puedeTrasbordo($linea, $ValorBoleto)
    {
        if ($this->UltimoColectivo == $linea || $this->UltimoValorPagado == 0.0 || $this->Ultimotrasbordo) {
            $this->Ultimotrasbordo = 0;
            return $ValorBoleto;
        }
        if ($this->dependeHora()) {
            if (($this->tiempo->time() - $this->UltimaHora) < 3600) {
                $this->Ultimotrasbordo = 1;
                return ($ValorBoleto * 0.33);
            }
        } else {
            if (($this->tiempo->time() - $this->UltimaHora) < 5400) {
                $this->Ultimotrasbordo = 1;
                return ($ValorBoleto * 0.33);
            }
        }
        $this->Ultimotrasbordo = 0;
        return $ValorBoleto;
    }

    /**
     * Dependiendo de la hora y el dia que sea puede haber un maximo de tiempo de 60 o 90 minutos.
     *
     * @return bool
     *   True si son 60 o false si son 90.
     */
    protected function dependeHora()
    {
        if ($this->tiempo->esFeriado() || date('N', $this->tiempo->time()) == 7){
            return false;
        }
        if (date('N', $this->tiempo->time()) == 6){
            if (date('G', $this->tiempo->time()) > 6 && date('G', $this->tiempo->time()) < 14){
                return true;
            } else {
                return false;
            }
        } else {
            if (date('G', $this->tiempo->time()) > 6 && date('G', $this->tiempo->time()) < 22){
                return true;
            } else {
                return false;
            }
        }
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
