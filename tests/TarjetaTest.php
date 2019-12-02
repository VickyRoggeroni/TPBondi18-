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
     * Comprueba que se puede recargargar el viaje plus y que se descuentan cuando se recarga la tarjeta
     */
    public function testRecargarPlus()
    {
        $tiempo = new Tiempo;
        $tarjeta = new Tarjeta(0, $tiempo);

        $this->assertTrue($tarjeta->recargar(50));
        $this->assertEquals($tarjeta->restarSaldo("153"), true);            //50-32,5=17,5
        $this->assertEquals($tarjeta->restarSaldo("153"), true);            //No le alcanza, viaje plus
        $this->assertEquals($tarjeta->restarSaldo("153"), true);            //No le alcanza, viaje plus
        $this->assertEquals($tarjeta->restarSaldo("153"), false);           //No le alcanza, no tiene viaje plus
        $this->assertEquals($tarjeta->obtenerSaldo(), 17.5);
        $this->assertTrue($tarjeta->recargar(100));                         //17,5 + 100 - (dos viajes plus) = 52.5
        $this->assertEquals($tarjeta->obtenerSaldo(), 52.5);
        $this->assertTrue($tarjeta->restarSaldo("153"));
        $this->assertEquals($tarjeta->obtenerSaldo(), 20);
        $this->assertTrue($tarjeta->restarSaldo("153"));            //Viaje plus
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
        
        //Pruebo pagar un trasbordo en un MISMO colectivo
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
        $tiempo->avanzar(2300);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 135);

        //Prueba pagar trasbordo un dia de semana normal antes de los 60 minutos (y antes de las 22hrs)
        $tiempo->avanzar(60800);
        $this->assertEquals(date('d-m', $tiempo->time()), "02-01");
        $tiempo->avanzar(43200);
        $this->assertEquals(date('G', $tiempo->time()), 15);            // Verifica hora (15hrs)
        $this->assertEquals(date('N', $tiempo->time()), 5);             // Verifica VIERNES
        $colectivo1->pagarCon($tarjeta);                                // Paga boleto
        $this->assertEquals($tarjeta->obtenerSaldo(), 102.5);
        $tiempo->avanzar(3550);                                         // Avanza menos de una hora
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 102.5);
        
        //Prueba pagar trasbordo un dia normal despues de los 60 minutos
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 70);
        $tiempo->avanzar(5300);
        $this->assertEquals(date('N', $tiempo->time()), 5);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 37.5);

    } //ANDAAAAA
    
//     Pruebo pagar un trasbordo en distintos colectivos con tiempo normal
    public function testVariosTransbordos()
    {
        $tiempo = new Tiempo();
        $tiempo->agregarFeriado("01-01-20");
        $this->AssertFalse($tiempo->EsFeriado());
        $tarjeta = new Tarjeta(0, $tiempo);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        $colectivo3 = new Colectivo(155, "RosarioBus", 33);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
        $colectivo3->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);
    }//ANNNDAAAa

// Pruebo que el transbordo sea vlido por 2 horas cuando es entre lass 22hrs y las 6hrs
    public function testTransbordoDeNoche()
    {
        $tiempo = new TiempoFalso();
        $tarjeta = new Tarjeta(0, $tiempo);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        $tarjeta->recargar(100);
        $this->assertEquals(date('N', $tiempo->time()), 4);             //Verifica dia
        $tiempo->avanzar(9000);                                         //Avanza para que sean las 02:30
        $this->assertEquals(date('H:i', $tiempo->time()), "02:30");     //Verifica hora
        $colectivo1->pagarCon($tarjeta);                                //Paga el colectivo
        $this->assertEquals($tarjeta->obtenerSaldo(), 67.5);            //Verifica saldo
        $tiempo->avanzar(5400);                                         //Avanza hora y media
        $colectivo2->pagarCon($tarjeta);                                //Paga nuevo colectivo
        $this->assertEquals($tarjeta->obtenerSaldo(), 67.5);            //Verifica saldo
        
        
        // Verifica que dsp de las 6 de la mañana el transbordo es por una hora otra vez
        $tiempo->avanzar(9000);                                         //Avanza dos horas y media
        $this->assertEquals(date('H:i', $tiempo->time()), "06:30");     //Verifica hora
        $colectivo2->pagarCon($tarjeta);                                //Paga nuevo colectivo
        $this->assertEquals($tarjeta->obtenerSaldo(), 35);              //Verifica saldo
        $tiempo->avanzar(5400);                                         //Avanza hora y media
        $colectivo2->pagarCon($tarjeta);                                //Paga nuevo colectivo
        $this->assertEquals($tarjeta->obtenerSaldo(), 2.5);             //Verifica saldo
        
    }
    
  // Pruebo que el transbordo es valido por 2 hrs los fines de semana
    public function testTransbordoFinde()
    {
        
        $tiempo = new TiempoFalso(0);
        $tiempo->agregarFeriado("01-06");
        $tarjeta = new Tarjeta(0, $tiempo);
        $tiempo->avanzar(28800);
        $tarjeta->recargar(100);
        $tarjeta->recargar(100);
        $colectivo1 = new Colectivo(122, "Semtur", 37);
        $colectivo2 = new Colectivo(134, "RosarioBus", 52);
        
        //Prueba pagar trasbordo un sabado a la tarde antes de las dos horas
        $this->assertEquals(date('N', $tiempo->time()), 4);         // Verifica el dia
        $tiempo->avanzar(86400);                                    // Avanza 24hrs
        $this->assertEquals(date('N', $tiempo->time()), 5);         // Verifica el dia otra vez
        $tiempo->avanzar(86400);                                    // Avanza otras 24 hrs
        $this->assertEquals(date('N', $tiempo->time()), 6);         // SABADO
        $colectivo1->pagarCon($tarjeta);                            // Paga el boleto
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);       
        $tiempo->avanzar(5400);                                     // Avanza 90mins
        $this->assertEquals(date('N', $tiempo->time()), 6);
        $this->assertEquals(date('G', $tiempo->time()), 9);         // Verifica hora
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);       // Verifica transbordo
        
        //Prueba pagar trasbordo un domingo antes de las dos horas
        $tiempo->avanzar(86400);                                    // Avanza 24hrs
        $this->assertEquals(date('N', $tiempo->time()), 7);         // DOMINGO
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 135);         // Paga boleto
        $tiempo->avanzar(5400);                                     // Avanza 90mins
        $colectivo2->pagarCon($tarjeta);                            // Hace transbordo
        $this->assertEquals($tarjeta->obtenerSaldo(), 135);         // Verifica boleto
    }
    
   // Pruebo que el transbordo es valido por 2 horas cuando es feriado
    public function testTransbordoFeriado()
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
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);                   // 200 - 32.5
        $tiempo->avanzar(4200);                                                 //avanza los 90 minutos
        $boleto2 = $colectivo2->pagarCon($tarjeta);                             //paga viaje transbordo gratis
        $this->assertEquals($tarjeta->obtenerSaldo(), 167.5);                   //el saldo se mantiene igual
        
        //Pruebo pagar un trasbordo un dia feriado cuando ya pasaron las 2 horas
        $tiempo->avanzar(7200);
        $colectivo1->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 135);
        $tiempo->avanzar(7300);
        $colectivo2->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->obtenerSaldo(), 102.5);
    }    
    
}

