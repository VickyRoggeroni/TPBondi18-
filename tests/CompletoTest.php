<?php
namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class CompletoTest extends TestCase
{

    /**
     * Comprueba que la tarjeta con franquicia completa pueda pagar boletos infinitos
     */
    public function testRestarBoletos()
    {
        $tiempo = new Tiempo;
        $completo = new Completo(0, $tiempo);
        for (($i = 0); $i < 160; ++$i) {
            $this->assertEquals($completo->restarSaldo("153"), true);
        }
        $this->assertEquals($completo->restarSaldo("153"), true);
    }
}
