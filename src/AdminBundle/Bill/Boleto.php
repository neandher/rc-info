<?php

namespace AdminBundle\Bill;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\Company;
use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
use Eduardokum\LaravelBoleto\Boleto\Render\Html;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Pessoa;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\HttpFoundation\Response;

class Boleto
{
    /**
     * @var FilesystemMap
     */
    private $fs;

    /**
     * @var string
     */
    private $logoPath;

    /**
     * Boleto constructor.
     * @param $logoPath
     * @param FilesystemMap $fs
     */
    public function __construct(FilesystemMap $fs, $logoPath)
    {
        $this->fs = $fs->get('boletos_fs');
        $this->logoPath = $logoPath;
    }

    public function renderPdf(Bill $bill, Company $company)
    {
        $beneficiario = new Pessoa([
            'nome' => $company->getNomeFantasia(),
            'endereco' => $company->getStreet(),
            'cep' => $company->getZipCode(),
            'uf' => $company->getUf()->getSigla(),
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
            'logo' => $this->logoPath,
            'dataVencimento' => new Carbon($bill->getDueDateAt()->format('Y/m/d')),
            'valor' => $bill->getAmount(),
            'multa' => false,
            'juros' => false,
            'numero' => $bill->getId(),
            'numeroDocumento' => $bill->getId(),
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => $company->getCarteira(),
            'codigoCliente' => $company->getCodigoCliente(),
            'agencia' => $company->getAgencia(),
            'conta' => $company->getCodigoCliente(),
            'descricaoDemonstrativo' => [
                'MULTA DE R$: ' . number_format($company->getMulta(), 2, ',', '.') . ' APÓS : ' . $company->getPrazoAposVencimento(),
                'JUROS DE R$: ' . number_format($company->getJuros(), 2, ',', '.') . ' AO DIA',
                'NÃO RECEBER APÓS ' . $company->getPrazoAposVencimento() . ' DIAS DO VENCIMENTO',
                'ATENÇÃO após efetuar o pagamento entre em contato com nosso escritório e retire sua senha de liberação 33499130',
                'Título sujeito a protesto | Link para atualização de vencimento | bloquetoexpresso.caixa.gov.br'
            ],
            'instrucoes' => [
                'MULTA DE R$: ' . number_format($company->getMulta(), 2, ',', '.') . ' APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: ' . number_format($company->getJuros(), 2, ',', '.') . ' AO DIA',
                'NÃO RECEBER APÓS ' . $company->getPrazoAposVencimento() . ' DIAS DO VENCIMENTO',
                'Link para atualização de vencimento',
                'bloquetoexpresso.caixa.gov.br'
            ],
            'aceite' => $company->getAceite(),
            'especieDoc' => $company->getEspecieDoc(),
        ];

        $boleto = new Caixa($boletoArray);
        $boleto->setLocalPagamento('PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE');
        
        $pdf = new Pdf();
        $pdf->addBoleto($boleto);
        
        $content = $pdf->gerarBoleto($pdf::OUTPUT_STRING);
        $this->fs->write($this->getBoletoFileName($bill), $content, true);
    }

    public function download(Bill $bill, $inline = false)
    {
        if ($this->fs->has('/' . $this->getBoletoFileName($bill))) {

            $file = $this->fs->get('/' . $this->getBoletoFileName($bill));

            return new Response(
                $file->getContent(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => ($inline ? 'inline; ' : 'attachment;')
                        . ' filename="' . $this->getBoletoFileName($bill) . '"'
                ]
            );
        }
        return false;
    }

    public function getBoletoFileName(Bill $bill)
    {
        return 'fatura_' . $bill->getId() . '.pdf';
    }
}