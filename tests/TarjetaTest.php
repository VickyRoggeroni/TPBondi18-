<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase
{

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */
    public function testCargaSaldo()
    {
        $tiempo = new Tiempo();

        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertTrue($tarjeta->recargar(10));
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);

        $this->assertTrue($tarjeta->recargar(20));
        $this->assertEquals($tarjeta->obtenerSaldo(), 30);

        $this->assertTrue($tarjeta->recargar(1119.90));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1330);

        $this->assertTrue($tarjeta->recargar(2114.11));
        $this->assertEquals($tarjeta->obtenerSaldo(), 3930);

        $this->assertTrue($tarjeta->recargar(30));
        $this->assertEquals($tarjeta->obtenerSaldo(), 3960);

        $this->assertTrue($tarjeta->recargar(50));
        $this->assertEquals($tarjeta->obtenerSaldo(), 4010);

        $this->assertTrue($tarjeta->recargar(100));
        $this->assertEquals($tarjeta->obtenerSaldo(), 4110);

    } // ESTE TEST ANDAAAAAAAAAAAA
    

    /**
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
    */
    public function testCargaSaldoInvalido()
    {
        $tiempo = new Tiempo();
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertFalse($tarjeta->recargar(15.0));
        $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    } // ESTE TEST TAMBIEEEENNNN
    
    /*
     * Comprueba que la tarjeta tiene viajes plus
     */
    public function testViajesPlus()
    {
        $tiempo = new Tiempo();
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertTrue($tarjeta->recargar(50));
        $this->assertEquals($tarjeta->obtenerSaldo(), 50);

        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->obtenerSaldo(), 17.5);

        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->restarSaldo("153"), false);
    } //ANDAAAAAAAA
    
    /*
     * Comprueba que se puede recargargar el viaje plus
     */
    public function testRecargarPlus()
    {
        $tiempo = new Tiempo;
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertTrue($tarjeta->recargar(50));
        $this->assertEquals($tarjeta->restarSaldo("153"), true);            //50-32,5=17,5
        $this->assertEquals($tarjeta->restarSaldo("153"), true);            //No le alcanza, viaje plus
        $this->assertEquals($tarjeta->restarSaldo("153"), true);            //No le alcanza, viaje plus
        $this->assertEquals($tarjeta->restarSaldo("153"), false);            //No le alcanza, no tiene viaje plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 17.5);
        $this->assertTrue($tarjeta->recargar(30));                          //17,5+30=47,5
        $this->assertEquals($tarjeta->obtenerSaldo(), 15);
        $this->assertTrue($tarjeta->restarSaldo("153"));            //47,5-32,5=15
        $this->assertFalse($tarjeta->restarSaldo("153"));            //viaje plus
    } //ANDAAAAAAAA
    
    /*
    Pruebo muchas cosas de trasbordo, con respecto al funcionamiento con el tiempo
    */
    public function testTrasbordo()
    {
       $tiempo = new TiempoFalso(0);
        $tiempo->agregarFeriado("01-06");
        $tarjeta = new Tarjeta(0, $tiempo);
        $tiempo->avanzar(28800);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        //Pruebo pagar un trasbordo un dia feriado con 90 minutos de espera y el texto del boleto
        $boleto = $colectivo1->pagarCon($tarjeta);                              //Paga un viaje
        $this->assertEquals(date('N', $tiempo->time()), '4');
        $this->assertEquals(date('G', $tiempo->time()), '8');
        $this->assertEquals(date('d-m', $tiempo->time()), "01-01");
        $this->assertEquals($boleto->obtenerFecha(), "01/01/1970 08:00:00");
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);                   // 200 -32.5
        $tiempo->avanzar(4200);                                                 //avanza los 90 minutos
        $boleto2 = $colectivo2->pagarCon($tarjeta);                             //paga viaje transbordo gratis
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);                   //el saldo se mantiene igual
        //Pruebo pagar un trasbordo en un mismo colectivo
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);                                        //paga otro viaje
        $this->assertEquals($tarjeta->obtenerSaldo(), 135);
        $tiempo->avanzar(2300);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 102.5);
        //Pruebo pagar un trasbordo un dia feriado cuando ya pasaron las 2 horas
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 70);
        $tiempo->avanzar(7300);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 37.5);
        //Prueba pagar trasbordo un dia normal antes de los 60 minutos
        $tiempo->avanzar(60800);
        $this->assertEquals(date('d-m', $tiempo->time()), "02-01");
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 5);
        $tiempo->avanzar(3550);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 5);
        //Prueba pagar trasbordo un dia normal despues de los 60 minutos
        $tiempo->avanzar(7200);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 172.5);
        $tiempo->avanzar(5300);
        $this->assertEquals(date('N', $tiempo->time()), 5);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 140);
        //Prueba pagar trasbordo un sabado a la tarde antes de las dos horas
        $tiempo->avanzar(68400);        
        $this->assertEquals(date('N', $tiempo->time()), 6);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 107.5);
        $tiempo->avanzar(5200);
        $this->assertEquals(date('N', $tiempo->time()), 6);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 107.5);
        //Prueba pagar trasbordo un domingo antes de las dos horas
        $tiempo->avanzar(104400);
        $this->assertEquals(date('N', $tiempo->time()), 7);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 75);
        $tiempo->avanzar(5200);
        $this->assertEquals(date('N', $tiempo->time()), 7);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 75);
    }
    
//     Pruebo pagar un trasbordo en distintos colectivos con tiempo normal
    public function testUnTrasbordo()
    {
        $tiempo = new Tiempo();
        $tiempo->agregarFeriado("01-01-20");
        $this->AssertFalse($tiempo->esFeriado());
        $tarjeta = new Tarjeta(0, $tiempo);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        $colectivo3 = new Colectivo(155, "RosarioBus", 33);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 180.316);
        $colectivo3->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 165.516);
    }


}

