<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class LeilaoDaoMock extends LeilaoDao
{
    private $leiloes = [];

    public function salva(Leilao $leilao): void
    {
        $this->leiloes[] = $leilao;
    }

    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return $leilao->estaFinalizado();
        });
    }

    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function (Leilao $leilao) {
            return !$leilao->estaFinalizado();
        });
    }

    public function atualiza(Leilao $leilao)
    {
        return;
    }
}

class EncerradorTest extends TestCase
{
    public function testDeveEncerrarLeiloesComMaisDeUmaSemana()
    {
        $leilaoFiat = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $leilaoVariante = new Leilao('Variante 0Km', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = new LeilaoDaoMock();
        $leilaoDao->salva($leilaoFiat);
        $leilaoDao->salva($leilaoVariante);

        $encerrador = new Encerrador($leilaoDao);
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
 * 1.4 Encerrando Leilões
 *
 * O código acaba sendo mais amplo que um teste de unidade e
 * acaba sendo um teste de integração por também testar o banco,
 * gerando uma falha após o segundo teste
 *
 * 1.6 Criando um dublê de teste
 *
 * Mocks simulam se métodos foram chamados corretamente pelos objetos dependentes.
 * eles simulam e substituem um objeto para verificar se as interações com o código
 * em teste acontecem como esperado.
 *
 * Nesse casa o LeilaoDaoMock simula o LeilaoDao, assim os dados gerados pelo teste não
 * são armazenados no BD, resolvendo a necessidade de testar somente a unidade.
 */