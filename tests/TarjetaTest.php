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

        $this->assertTrue($tarjeta->recargar(510.15));
        $this->assertEquals($tarjeta->obtenerSaldo(), 622.08);

        $this->assertTrue($tarjeta->recargar(962.59));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1806.25);

        $this->assertTrue($tarjeta->recargar(30));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1836.25);

        $this->assertTrue($tarjeta->recargar(50));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1886.25);

        $this->assertTrue($tarjeta->recargar(100));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1986.25);
    }

    /**
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido()
    {
        $tiempo = new Tiempo();
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertFalse($tarjeta->recargar(15));
        $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }
    /*
     * Comprueba que la tarjeta tiene viajes plus
     */
    public function testViajesPlus()
    {
        $tiempo = new Tiempo();
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertTrue($tarjeta->recargar(20));
        $this->assertEquals($tarjeta->obtenerSaldo(), 20);

        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2);

        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->restarSaldo("153"), false);
    }

    /*
     * Comprueba que se puede recargargar el viaje plus
     */
    public function testRecargarPlus()
    {
        $tiempo = new Tiempo;
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertTrue($tarjeta->recargar(20));
        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2);
        $this->assertTrue($tarjeta->recargar(10));
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4);
        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->restarSaldo("153"), true);
        $this->assertEquals($tarjeta->obtenerSaldo(), 0.4);
        $this->assertEquals($tarjeta->restarSaldo("153"), false);
    }

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
        $boleto = $colectivo1->pagarCon($tarjeta);
        $this->assertEquals(date('N', $tiempo->time()), '4');
        $this->assertEquals(date('G', $tiempo->time()), '8');
        $this->assertEquals(date('d-m', $tiempo->time()), "01-01");
        $this->assertEquals($boleto->obtenerFecha(), "01/01/1970 08:00:00");
        $this->assertEquals($tarjeta->obtenerSaldo(), 185.2);
        $tiempo->avanzar(4200);
        $boleto2 = $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($boleto2->obtenerDescripcion(), "Trasbordo Normal 4.884");
        $this->assertEquals($tarjeta->obtenerSaldo(), 180.316);

        //Pruebo pagar un trasbordo en un mismo colectivo
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 165.516);
        $tiempo->avanzar(2300);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 150.716);

        //Pruebo pagar un trasbordo un dia feriado cuando ya pasaron los 90 minutos
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 135.916);
        $tiempo->avanzar(5500);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 121.116);

        //Prueba pagar trasbordo un dia normal antes de los 60 minutos
        $tiempo->avanzar(60800);
        $this->assertEquals(date('d-m', $tiempo->time()), "02-01");
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 106.316);
        $tiempo->avanzar(3550);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 101.432);

        //Prueba pagar trasbordo un dia normal despues de los 60 minutos
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 86.632);
        $tiempo->avanzar(5300);
        $this->assertEquals(date('N', $tiempo->time()), 5);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 71.832);

        //Prueba pagar trasbordo un sabado a la mañana despues de los 60 minutos
        $tiempo->avanzar(64800);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 57.032);
        $tiempo->avanzar(4100);
        $this->assertEquals(date('N', $tiempo->time()), 6);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 42.232);

        //Prueba pagar trasbordo un sabado a la mañana despues de los 60 minutos
        $tiempo->avanzar(28800);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 27.432);
        $tiempo->avanzar(5200);
        $this->assertEquals(date('N', $tiempo->time()), 6);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 22.548);

        //Prueba pagar trasbordo un domingo despues de los 60 minutos
        $this->assertTrue($tarjeta->recargar(100));
        $tiempo->avanzar(57600);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 107.748);
        $tiempo->avanzar(5200);
        $this->assertEquals(date('N', $tiempo->time()), 7);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 102.864);
    }

    /*
    Pruebo pagar un trasbordo en distintos colectivos con tiempo normal
     */
    public function testUnTrasbordo()
    {
        $tiempo = new Tiempo();
        $tiempo->agregarFeriado("01-01-18");
        $this->AssertFalse($tiempo->esFeriado());
        $tarjeta = new Tarjeta(0, $tiempo);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        $colectivo3 = new Colectivo(155, "RosarioBus", 33);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 185.2);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 180.316);
        $colectivo3->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 165.516);
    }

    /*
    Pruebo pagar un trasbordo en distintos colectivos con tiempo normal
     */
    public function testTrasbordo2()
    {
        $tiempo = new Tiempo();
        $tiempo->agregarFeriado("01-01-18");
        $this->AssertFalse($tiempo->esFeriado());
        $tarjeta = new Tarjeta(0, $tiempo);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 185.2);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 180.316);

    }
}
