<?php

namespace AdminBundle\Bill;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\BillRemessa;
use AdminBundle\Entity\Company;
use Carbon\Carbon;
use Cnab\Banco;
use Cnab\Especie;
use Cnab\Remessa\Cnab240\Arquivo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
use Eduardokum\LaravelBoleto\Boleto\Render\Html;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Pessoa;
use Symfony\Component\HttpFoundation\Response;

class Remessa
{
    private $remessasPath;

    /**
     * Boleto constructor.
     * @param $remessasPath
     */
    public function __construct($remessasPath)
    {
        $this->remessasPath = $remessasPath;
    }

    /**
     * @param ArrayCollection|Bill $bills
     * @param $remessaId
     */
    public function save($bills, $remessaId)
    {
        $company = new Company();

        $arquivo = new \CnabPHP\Remessa(104, 'cnab240_SIGCB', [
            'nome_empresa' => $company->getRazaoSocial(), // seu nome de empresa
            'tipo_inscricao' => 2, // 1 para cpf, 2 cnpj
            'numero_inscricao' => $company->getCnpj(), // seu cpf ou cnpj completo
            'agencia' => $company->getAgencia(), // agencia sem o digito verificador
            'agencia_dv' => $company->getAgenciaDigito(), // somente o digito verificador da agencia
            'conta' => $company->getConta(), // número da conta
            'conta_dv' => $company->getContaDigito(), // digito da conta
            'codigo_beneficiario' => $company->getBoletoCodigoCliente(), // codigo fornecido pelo banco
            'numero_sequencial_arquivo' => $remessaId, // sequencial do arquivo um numero novo para cada arquivo gerado
        ]);

        $lote = $arquivo->addLote(array('tipo_servico' => 1)); // tipo_servico  = 1 para cobrança registrada, 2 para sem registro

        /** @var $bill $bill */
        foreach ($bills as $bill) {

            $lote->inserirDetalhe([

                'codigo_movimento' => 1, //1 = Entrada de título, para outras opçoes ver nota explicativa C004 manual Cnab_SIGCB na pasta docs
                'nosso_numero' => $bill->getId(), // numero sequencial de boleto
                //'seu_numero' => 43,// se nao informado usarei o nosso numero

                'especie_titulo' => "DM", // informar dm e sera convertido para codigo em qualquer laytou conferir em especie.php
                'valor' => $bill->getAmount(), // Valor do boleto como float valido em php
                'emissao_boleto' => 2, // tipo de emissao do boleto informar 2 para emissao pelo beneficiario e 1 para emissao pelo banco
                'protestar' => 3, // 1 = Protestar com (Prazo) dias, 3 = Devolver ap�s (Prazo) dias
                'prazo_protesto' => 5, // Informar o numero de dias apos o vencimento para iniciar o protesto
                'nome_pagador' => $bill->getCustomer()->getName(), // O Pagador � o cliente, preste atenção nos campos abaixo
                'tipo_inscricao' => 2, //campo fixo, escreva '1' se for pessoa fisica, 2 se for pessoa juridica
                'numero_inscricao' => $bill->getCustomer()->getCnpj(),//cpf ou ncpj do pagador
                'endereco_pagador' => $bill->getCustomer()->getMainAddress()->getStreet(),
                'bairro_pagador' => $bill->getCustomer()->getMainAddress()->getDistrict(),
                'cep_pagador' => $bill->getCustomer()->getMainAddress()->getZipCode(), // com h�fem
                'cidade_pagador' => $bill->getCustomer()->getMainAddress()->getCity(),
                'uf_pagador' => $bill->getCustomer()->getMainAddress()->getUf()->getSigla(),
                'data_vencimento' => $bill->getDueDateAt()->format('Y-m-d'), // informar a data neste formato
                'data_emissao' => $bill->getCreatedAt()->format('Y-m-d'), // informar a data neste formato
                'vlr_juros' => 0.81, // Valor do juros de 1 dia'
                'data_desconto' => $bill->getDueDateAt()->format('Y-m-d'), // informar a data neste formato
                'vlr_desconto' => '0', // Valor do desconto
                'baixar' => 1, // codigo para indicar o tipo de baixa '1' (Baixar/ Devolver) ou '2' (N�o Baixar / N�o Devolver)
                'prazo_baixa' => 120, // prazo de dias para o cliente pagar ap�s o vencimento
                'mensagem' => 'JUROS de R$0,81 ao dia' . PHP_EOL . "Não receber apos 120 dias",
                'email_pagador' => $bill->getCustomer()->getEmail(), // data da multa
                'data_multa' => $bill->getDueDateAt()->add(new \DateInterval('P1D'))->format('Y-m-d'), // informar a data neste formato, // data da multa
                'vlr_multa' => 4.94, // valor da multa
            ]);
        }

        $text = $arquivo->getText();
        $filename = $this->remessasPath . '/remessa_' . $remessaId . '.REM';

        file_put_contents($filename, $text);
    }

    public function download(BillRemessa $billRemessa)
    {
        $file = $this->remessasPath . '/remessa_' . $billRemessa->getId() . '.REM';

        if (file_exists($file)) {
            return new Response(
                file_get_contents($file),
                200,
                [
                    'Content-Type' => 'application/txt',
                    'Content-Disposition' => 'inline; filename="remessa_' . $billRemessa->getId() . '.REM"'
                ]
            );
        }
        return false;
    }

    /*private function modelo2(Bill $bill)
    {
        $company = new Company();

        $beneficiario = new Pessoa([
            'nome' => $company->getNomeFantasia(),
            'endereco' => $company->getAddressStreet(),
            'cep' => $company->getZipCode(),
            'uf' => $company->getUf(),
            'cidade' => $company->getCity(),
            'documento' => $company->getCnpj(),
        ]);

        $pagador = new Pessoa([
            'nome' => $bill->getCustomer()->getName(),
            'endereco' => $bill->getCustomer()->getMainAddress()->getStreet(),
            'bairro' => $bill->getCustomer()->getMainAddress()->getDistrict(),
            'cep' => $bill->getCustomer()->getMainAddress()->getZipCode(),
            'uf' => $bill->getCustomer()->getMainAddress()->getUf()->getSigla(),
            'cidade' => $bill->getCustomer()->getMainAddress()->getCity(),
            'documento' => $bill->getCustomer()->getCnpj(),
        ]);

        $boletoArray = [
            'logo' => false,
            'dataVencimento' => new Carbon($bill->getDueDateAt()->format('Y/m/d')),
            'valor' => $bill->getAmount(),
            'multa' => false,
            'juros' => false,
            'numero' => $bill->getId(),
            'numeroDocumento' => $bill->getId(),
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => $company->getBoletoCarteira(),
            'codigoCliente' => $company->getBoletoCodigoCliente(),
            'agencia' => $company->getAgencia(),
            'conta' => $company->getBoletoCodigoCliente(),
            'descricaoDemonstrativo' => [
                'MULTA DE R$: 4,94 APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: 0,81 AO DIA',
                'NÃO RECEBER APÓS 120 DIAS DO VENCIMENTO',
                'ATENÇÃO após efetuar o pagamento entre em contato com nosso escritório e retire sua senha de liberação 33499130',
                'Título sujeito a protesto | Link para atualização de vencimento | bloquetoexpresso.caixa.gov.br'
            ],
            'instrucoes' => [
                'MULTA DE R$: 4,94 APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: 0,81 AO DIA',
                'NÃO RECEBER APÓS 120 DIAS DO VENCIMENTO',
                'Link para atualização de vencimento',
                'bloquetoexpresso.caixa.gov.br'
            ],
            'aceite' => $company->getBoletoAceite(),
            'especieDoc' => $company->getBoletoEspecieDoc(),
        ];

        $boleto = new Caixa($boletoArray);
        $boleto->setLocalPagamento('PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE');

        $remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Caixa(
            [
                'agencia' => $company->getAgencia(),
                'idRemessa' => $remessaId,
                'conta' => $company->getConta(),
                'carteira' => 'RG',
                'codigoCliente' => $company->getBoletoCodigoCliente(),
                'beneficiario' => $beneficiario,
            ]
        );

        var_dump($remessa);
        exit;

        $remessa->addBoleto($boleto);
        echo $remessa->save($this->remessasPath . '/remessa_m1_' . $remessaId . '.txt');
    }*/

    /*private function modelo1(Bill $bill)
    {
        $company = new Company();

        $codigoBanco = Banco::CEF;

        $arquivo = new Arquivo($codigoBanco, 'sigcb');

        $arquivo->configure([
            'data_geracao' => new \DateTime(),
            'data_gravacao' => new \DateTime(),
            'nome_fantasia' => $company->getNomeFantasia(), // seu nome de empresa
            'razao_social' => $company->getRazaoSocial(), // sua razão social
            'cnpj' => $company->getCnpj(), // seu cnpj completo
            'banco' => $codigoBanco, //código do banco
            'logradouro' => $company->getAddressStreet(),
            'numero' => $company->getAddressNumber(),
            'bairro' => $company->getDistrict(),
            'cidade' => $company->getCity(),
            'uf' => $company->getUf(),
            'cep' => $company->getZipCode(),
            'agencia' => $company->getAgencia(),
            'agencia_dv' => '1',
            'operacao' => '003',
            'conta' => $company->getConta(), // número da conta
            //'conta_cedente_dv' => '', // digito da conta
            'codigo_cedente' => $this->trataString($company->getBoletoCodigoCliente()),
            'numero_sequencial_arquivo' => $remessaId
        ]);

        $arquivo->insertDetalhe(array(
            'modalidade_carteira' => 'RG',
            'aceite' => 'S',
            'registrado' => true,
            'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
            'nosso_numero' => $bill->getId(),
            'numero_documento' => $bill->getId(),
            'especie' => Especie::CEF_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie
            'valor' => $bill->getAmount(), // Valor do boleto
            'instrucao1' => 0,
            'instrucao2' => 0,
            'sacado_nome' => $bill->getCustomer()->getName(),
            'sacado_tipo' => 'cnpj', //campo fixo, escreva 'cpf' (sim as letras cpf)
            'sacado_cpf' => $bill->getCustomer()->getCnpj(),
            'sacado_logradouro' => $bill->getCustomer()->getMainAddress()->getStreet(),
            'sacado_bairro' => $bill->getCustomer()->getMainAddress()->getDistrict(),
            'sacado_cep' => $bill->getCustomer()->getMainAddress()->getZipCode(), // sem hífem
            'sacado_cidade' => $bill->getCustomer()->getMainAddress()->getCity(),
            'sacado_uf' => $bill->getCustomer()->getMainAddress()->getUf()->getSigla(),
            'data_vencimento' => $bill->getDueDateAt()->format('Y-m-d'),
            'data_cadastro' => $bill->getCreatedAt()->format('Y-m-d'),
            'juros_de_um_dia' => 0,
            'valor_desconto' => 0, // Valor do desconto
            'data_desconto' => 0,
            'prazo' => 120, // prazo de dias para o cliente pagar após o vencimento
            'taxa_de_permanencia' => '0',
            'mensagem' => ' ',
            'data_multa' => $bill->getDueDateAt()->format('Y-m-d'),
            'valor_multa' => 0,
            //'baixar_apos_dias' => 120,
            //'identificacao_distribuicao' => 0
        ));

        $arquivo->save($this->remessasPath . '/remessa_m2_' . $remessaId . '.txt');
    }*/

    /*private function trataString($string)
    {
        return str_replace(
            ['-'],
            [''],
            $string
        );
    }*/
}