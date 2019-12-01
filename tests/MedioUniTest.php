<?php
namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class MedioUniTest extends TestCase
{

    /**
     * Comprueba que la tarjeta con media franquicia Universitaria solo tiene 2 medios y que la restriccion de 5 minutos funciona
     */
    public function testRestarBoletos()
    {
        $tiempo = new TiempoFalso;
        $medio = new MedioUniversitario(0, $tiempo);
        $this->assertTrue($medio->recargar(100));
        $this->assertEquals($medio->obtenerSaldo(), 100);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 83.75);                 // Primer Medio
        $tiempo->avanzar(50);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 51.25);                 // Menos de un minuto, boleto normal
        $tiempo->avanzar(300);                                              // Avanza 5 min
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 35);                    // Segundo Medio
        $tiempo->avanzar(300);                                              // Avanza 5 min
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 2.5);                   // Ya no tiene mas medios, boleto normal
        $this->assertEquals($medio->restarSaldo("153"), true);              // Viaje Plus 1
        $this->assertEquals($medio->restarSaldo("153"), true);              // Viaje Plus 2
        $this->assertEquals($medio->restarSaldo("153"), false);             // Viaje Invalido
    }



    /**
     * Comprueba que la tarjeta con media franquicia Universitaria tiene 2 medios, y cuando pasa el dia se reinician
     */
    public function testPasoDia()
    {
        $tiempo = new TiempoFalso;
        $medio = new MedioUniversitario(0, $tiempo);
        $this->assertTrue($medio->recargar(100));
        $this->assertTrue($medio->recargar(100));
        $tiempo->avanzar(27000);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 183.75);            // Primer medio
        $tiempo->avanzar(10800);                                        // Avanza 3 hrs
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 167.5);             // Segundo medio
        $tiempo->avanzar(7200);                                         // Avanza 2 hrs
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 135);               // Boleto normal
        $tiempo->avanzar(86400);                                        // 24 hrs
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 118.75);            // Primer medio
        $tiempo->avanzar(7200);                                         // 2 hrs
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 102.5);             // Segundo medio
        $tiempo->avanzar(7200);                                         // 2 hrs
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 70);                // Viaje normal
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
        $this->assertEquals($tarjeta->obtenerSaldo(), 183.75);
        $tiempo->avanzar(4200);
        $boleto2 = $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 183.75);

        $tiempo->avanzar(38100);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals(date('d-m', $tiempo->time()), "01-01");
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
        $tiempo->avanzar(3500);
        $boleto2 = $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
    }
}
