<?php
namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class MedioTest extends TestCase
{

    /**
     * Comprueba que la tarjeta con media franquicia no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido()
    {
        $tiempo = new Tiempo;
        $medio = new Medio(0, $tiempo);
        $this->assertFalse($medio->recargar(15));
        $this->assertEquals($medio->obtenerSaldo(), 0);
    }

    /**
     * Comprueba que la tarjeta con media franquicia puede restar boletos, con la limitacion de tiempo
     */
    public function testRestarBoletos()
    {
        $tiempo = new TiempoFalso;
        $medio = new Medio(0, $tiempo);
        $this->assertTrue($medio->recargar(50));
        $this->assertTrue($medio->recargar(20));
        $this->assertEquals($medio->obtenerSaldo(), 70);
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 53.75);             //Resta un medio boleto
        /* Prueba que si pasaron menos de 5 minutos se cobra un boleto normal */
        $this->assertEquals($medio->restarSaldo("153"), true);
        $this->assertEquals($medio->obtenerSaldo(), 21.25);
        /* Y si pasaron los 5 min, resta medio */
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), true);          // Resta medio, quedan 5 pesos
        $tiempo->avanzar(300);                                          // Avanza 5 minutos
        /* Comprueba que los viajes plus andan */
        $this->assertEquals($medio->restarSaldo("153"), true);          //Viaje plus 1
        $this->assertEquals($medio->restarSaldo("153"), true);          //Viaje plus 2
        $tiempo->avanzar(300);
        $this->assertEquals($medio->restarSaldo("153"), false);         //Viaje invalido
    }


    /*
    Se testea si se puede pagar un trasbordo un dia feriado con 90 minutos de espera
     */
    public function testTrasbordoMedio()
    {
        $tiempo = new TiempoFalso(0);
        $tarjeta = new Medio(0, $tiempo);
        $tiempo->avanzar(28800);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);

        /*
        Pruebo pagar un trasbordo un dia feriado con 90 minutos de espera
         */
        $boleto = $colectivo1->pagarCon($tarjeta);
        $this->assertEquals(date('N', $tiempo->time()), '4');
        $this->assertEquals(date('G', $tiempo->time()), '8');
        $this->assertEquals(date('d-m', $tiempo->time()), "01-01");
        $this->assertEquals($boleto->obtenerFecha(), "01/01/1970 08:00:00");
        $this->assertEquals($tarjeta->obtenerSaldo(), 183.75);
        $tiempo->avanzar(4200);
        $boleto2 = $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 183.75);
    }
}
