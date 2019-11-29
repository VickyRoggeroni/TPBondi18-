<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase
{

    /**
     * Probamos la creacion del colectivo y la realizacion de un pago
     */
    public function testColectivo()
    {
        $colectivo = new Colectivo("102 Negro", "Semtur", 37);
        /*
        Probamos la asignacion de parametros iniciales
         */
        $this->assertEquals($colectivo->linea(), "102 Negro");
        $this->assertEquals($colectivo->empresa(), "Semtur");
        $this->assertEquals($colectivo->numero(), 37);
        /*
        Probamos la realizacion de una viaje
         
        $this->assertEquals($colectivo->pagarCon($tarjeta), new Boleto($colectivo, $tarjeta));

        $this->assertEquals($tarjeta->obtenerSaldo(), 559.58);

    }

    /**
     * Probamos la realizacion de un pago sin saldo y el uso de plus
   
    public function testSinSaldo()
    {

        $tiempo = new Tiempo;
        $tarjeta = new Tarjeta(0, $tiempo);
        $colectivo = new Colectivo(141, "Semtur", 37);
        
        Probamos la realizacion de una viaje sin saldo
         
        $this->assertEquals($colectivo->pagarCon($tarjeta), new Boleto($colectivo, $tarjeta));
        $this->assertEquals($colectivo->pagarCon($tarjeta), new Boleto($colectivo, $tarjeta));
        $this->assertEquals($colectivo->pagarCon($tarjeta), false);
    }
    */
}