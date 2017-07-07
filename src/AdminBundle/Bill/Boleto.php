<?php

namespace AdminBundle\Bill;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\Company;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
use Eduardokum\LaravelBoleto\Boleto\Render\Html;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Pessoa;
use Symfony\Component\HttpFoundation\Response;

class Boleto
{
    private $boletosPath;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Boleto constructor.
     * @param $boletosPath
     * @param EntityManagerInterface $em
     */
    public function __construct($boletosPath, EntityManagerInterface $em)
    {
        $this->boletosPath = $boletosPath;
        $this->em = $em;
    }

    public function renderPdf(Bill $bill, $save = false, $download = false)
    {
        $companys = $this->em->getRepository(Company::class)->findAll();

        if (!count($companys) > 0) {
            return;
        }

        $company = $companys[0];

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
            'logo' => false,
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

        $dadosBoleto = $boleto->toArray();
        $dadosBoleto['imprimir_carregamento'] = false;

        $html = new Html($dadosBoleto);
        $dadosBoleto['css'] = $html->writeCss();
        $dadosBoleto['codigo_barras'] = $html->getImagemCodigoDeBarras($dadosBoleto['codigo_barras']);

        $pdf = new Pdf();
        $pdf->addBoleto($boleto);

        if ($save) {
            $pdf->gerarBoleto(
                $pdf::OUTPUT_SAVE,
                $this->boletosPath . '/fatura_' . $bill->getId() . '.pdf'
            );
        } else {
            $pdf_inline = $pdf->gerarBoleto($pdf::OUTPUT_STRING);
            return new Response(
                $pdf_inline,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => ($download ? 'attachment; ' : '')
                        . 'inline; filename="fatura_' . $bill->getId() . '.pdf"'
                ]
            );
        }

        //return $this->render('admin/boleto/_boleto.html.twig', $dadosBoleto);
    }

    public function download(Bill $bill, $inline = false)
    {
        $file = $this->boletosPath . '/fatura_' . $bill->getId() . '.pdf';

        if (file_exists($file)) {
            return new Response(
                file_get_contents($file),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => ($inline ? 'inline; ' : 'attachment;')
                        . ' filename="fatura_' . $bill->getId() . '.pdf"'
                ]
            );
        }
        return false;
    }
}