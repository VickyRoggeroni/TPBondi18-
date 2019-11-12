<?php
namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class MedioUniTest extends TestCase
{

    /**
     * Comprueba que la tarjeta con media franquicia Universitaria puede restar boletos, solo 2 medios
     */
    public function testRestarBoletos()
    {
        $tiempo = new TiempoFalso;
        $medio = new MedioUniversitario(0, $tiempo);
        $this->assertTrue($medio->recargar(100));
        $this->assertEquals($medio->obtenerSaldo(), 100);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 92.6);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 85.2);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 70.4);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 55.60);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 40.80);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 26.00);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 11.20);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $this->assertEquals($medio->obtenerSaldo(), 11.20);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 11.20);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), false);

    }

    /**
     * Comprueba que la tarjeta con media franquicia Universitaria puede marcar una vez cada 5 minutos
     */
    public function testTiempoInvalido()
    {
        $tiempo = new TiempoFalso;
        $medio = new MedioUniversitario(0, $tiempo);
        $this->assertTrue($medio->recargar(962.59));
        $this->assertEquals($medio->restarSaldo("153"), true);
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $tiempo->avanzar(265);
        $this->assertEquals($medio->restarSaldo("153"), false);
        $tiempo->avanzar(584);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->restarSaldo("153"), false);
    }

    /**
     * Comprueba que la tarjeta con media franquicia Universitaria tiene 2 medios, y cuando pasa el dia se reinician
     */
    public function testPasoDia()
    {
        $tiempo = new TiempoFalso;
        $medio = new MedioUniversitario(0, $tiempo);
        $this->assertTrue($medio->recargar(100));
        $tiempo->avanzar(27000);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 92.6);
        $tiempo->avanzar(18000);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 85.2);
        $tiempo->avanzar(20000);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 70.4);
        $tiempo->avanzar(21500);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 63.0);
        $tiempo->avanzar(1500);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 55.6);
        $tiempo->avanzar(10000);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 40.8);
    }

    /*
    Pruebo pagar un trasbordo un dia feriado con 90 minutos de espera y el texto del boleto
     */
    public function testTrasbordoUni()
    {
        $tiempo = new TiempoFalso;
        $tarjeta = new MedioUniversitario(0, $tiempo);
        $tiempo->avanzar(42300);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);

        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 192.6);
        $tiempo->avanzar(4200);
        $boleto2 = $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($boleto2->obtenerDescripcion(), "Trasbordo Medio 2.442");
        $this->assertEquals($tarjeta->obtenerSaldo(), 190.158);

        $tiempo->avanzar(38100);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals(date('d-m', $tiempo->time()), "01-01");
        $this->assertEquals($tarjeta->obtenerSaldo(), 175.358);
        $tiempo->avanzar(3500);
        $boleto2 = $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($boleto2->obtenerDescripcion(), "Trasbordo Medio 2.442");
        $this->assertEquals($tarjeta->obtenerSaldo(), 172.916);
    }
}
