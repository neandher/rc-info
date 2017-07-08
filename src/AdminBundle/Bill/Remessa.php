<?php

namespace AdminBundle\Bill;

use AdminBundle\Entity\BillRemessa;
use AdminBundle\Entity\Company;
use Symfony\Component\HttpFoundation\Response;

class Remessa
{
    /**
     * @var string
     */
    private $remessasPath;

    /**
     * BillRemessa constructor.
     * @param $remessasPath
     */
    public function __construct($remessasPath)
    {
        $this->remessasPath = $remessasPath;
    }

    /**
     * @param BillRemessa $billRemessa
     * @param Company $company
     */
    public function renderRem(BillRemessa $billRemessa, Company $company)
    {
        $arquivo = new \CnabPHP\Remessa(104, 'cnab240_SIGCB', [
            'nome_empresa' => $company->getRazaoSocial(), // seu nome de empresa
            'tipo_inscricao' => 2, // 1 para cpf, 2 cnpj
            'numero_inscricao' => $company->getCnpj(), // seu cpf ou cnpj completo
            'agencia' => $company->getAgencia(), // agencia sem o digito verificador
            'agencia_dv' => $company->getAgenciaDigito(), // somente o digito verificador da agencia
            'conta' => $company->getConta(), // número da conta
            'conta_dv' => $company->getContaDigito(), // digito da conta
            'codigo_beneficiario' => $company->getCodigoCliente(), // codigo fornecido pelo banco
            'numero_sequencial_arquivo' => $billRemessa->getId(), // sequencial do arquivo um numero novo para cada arquivo gerado
        ]);

        $lote = $arquivo->addLote(array('tipo_servico' => 1)); // tipo_servico  = 1 para cobrança registrada, 2 para sem registro

        /** @var $bill $bill */
        foreach ($billRemessa->getBills() as $bill) {

            $lote->inserirDetalhe([

                'codigo_movimento' => 1, //1 = Entrada de título, para outras opçoes ver nota explicativa C004 manual Cnab_SIGCB na pasta docs
                'nosso_numero' => $bill->getId(), // numero sequencial de boleto
                //'seu_numero' => 43,// se nao informado usarei o nosso numero

                'especie_titulo' => $company->getEspecieDoc(), // informar dm e sera convertido para codigo em qualquer laytou conferir em especie.php
                'valor' => $bill->getAmount(), // Valor do boleto como float valido em php
                'emissao_boleto' => 2, // tipo de emissao do boleto informar 2 para emissao pelo beneficiario e 1 para emissao pelo banco
                'protestar' => $company->getCodigoProtesto(), // 1 = Protestar com (Prazo) dias, 3 = Devolver ap�s (Prazo) dias
                'prazo_protesto' => $company->getPrazoProtesto(), // Informar o numero de dias apos o vencimento para iniciar o protesto
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
                'vlr_juros' => $company->getJuros(), // Valor do juros de 1 dia'
                'data_desconto' => $bill->getDueDateAt()->format('Y-m-d'), // informar a data neste formato
                'vlr_desconto' => '0', // Valor do desconto
                'baixar' => $company->getCodigoBaixaDevolucao(), // codigo para indicar o tipo de baixa '1' (Baixar/ Devolver) ou '2' (N�o Baixar / N�o Devolver)
                'prazo_baixa' => $company->getPrazoBaixaDevolucao(), // prazo de dias para o cliente pagar ap�s o vencimento
                'mensagem' => 'JUROS de R$' . number_format($company->getJuros(), 2, '.', '') . ' ao dia' . PHP_EOL . "Não receber apos " . $company->getPrazoAposVencimento() . " dias",
                'email_pagador' => $bill->getCustomer()->getEmail(), // data da multa
                'data_multa' => $bill->getDueDateAt()->add(new \DateInterval('P1D'))->format('Y-m-d'), // informar a data neste formato, // data da multa
                'vlr_multa' => $company->getMulta(), // valor da multa
            ]);
        }

        $text = $arquivo->getText();
        $filename = $this->remessasPath . '/' . $this->getRemessaFileName($billRemessa);

        file_put_contents($filename, $text);
    }

    public function download(BillRemessa $billRemessa)
    {
        $file = $this->remessasPath . '/' . $this->getRemessaFileName($billRemessa);

        if (file_exists($file)) {
            return new Response(
                file_get_contents($file),
                200,
                [
                    'Content-Type' => 'application/txt',
                    'Content-Disposition' => 'inline; filename="' . $this->getRemessaFileName($billRemessa) . '"'
                ]
            );
        }
        return false;
    }

    public function getRemessaFileName(BillRemessa $billRemessa)
    {
        return 'remessa_' . $billRemessa->getId() . '.REM';
    }
}