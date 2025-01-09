<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = new LeilaoDao();
        $leilaoDao->salva($leilaoFiat);
        $leilaoDao->salva($leilaoVariante);

        $encerrador = new Encerrador();
        $encerrador->encerra();

        $leiloesEncerrados = $leilaoDao->recuperarFinalizados();
        static::assertCount(2, $leiloesEncerrados);
        static::assertEquals(
            'Fiat 147 0Km',
            $leiloesEncerrados[0]->recuperarDescricao()
        );
        static::assertEquals(
            'Variante 0Km',
            $leiloesEncerrados[1]->recuperarDescricao()
        );
    }
}

/*
 * 1,4 Encerrando Leilões
 *
 * O código acaba sendo mais amplo que um teste de unidade e
 * acaba sendo um teste de integração por também testar o banco,
 * gerando uma falha após o segundo teste
 */