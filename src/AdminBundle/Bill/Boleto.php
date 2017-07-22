<?php

namespace AdminBundle\Bill;

use AdminBundle\Entity\Bill;
use AdminBundle\Entity\Company;
use AdminBundle\Event\BillBoletoEvents;
use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
use Eduardokum\LaravelBoleto\Boleto\Render\Html;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Pessoa;
use Gedmo\Sluggable\Util\Urlizer;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Boleto constructor.
     * @param FilesystemMap $fs
     * @param $logoPath
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(FilesystemMap $fs, $logoPath, EventDispatcherInterface $dispatcher)
    {
        $this->fs = $fs->get('boletos_fs');
        $this->logoPath = $logoPath;
        $this->dispatcher = $dispatcher;
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
                'MULTA DE R$: ' . number_format($company->getMulta() * $bill->getAmount(), 2, ',', '.') . ' APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: ' . number_format($company->getJuros() * $bill->getAmount(), 2, ',', '.') . ' AO DIA',
                'NÃO RECEBER APÓS ' . $company->getPrazoAposVencimento() . ' DIAS DO VENCIMENTO',
                'ATENÇÃO após efetuar o pagamento entre em contato com nosso escritório e retire sua senha de liberação 33499130',
                'Título sujeito a protesto | Link para atualização de vencimento | bloquetoexpresso.caixa.gov.br'
            ],
            'instrucoes' => [
                'MULTA DE R$: ' . number_format($company->getMulta() * $bill->getAmount(), 2, ',', '.') . ' APÓS : ' . $bill->getDueDateAt()->format('d/m/Y'),
                'JUROS DE R$: ' . number_format($company->getJuros() * $bill->getAmount(), 2, ',', '.') . ' AO DIA',
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

        $boletoName = $this->getBoletoFileName($bill);

        $this->fs->write($boletoName, $content, true);

        $genericEvent = new GenericEvent($bill);
        $genericEvent->setArgument('boletoName', $boletoName);

        $this->dispatcher->dispatch(BillBoletoEvents::GENERATE_SUCCESS, $genericEvent);
    }

    public function download(Bill $bill, $inline = false)
    {
        if ($this->fs->has('/' . $bill->getBoletoName())) {

            $file = $this->fs->get('/' . $bill->getBoletoName());

            return new Response(
                $file->getContent(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => ($inline ? 'inline; ' : 'attachment;')
                        . ' filename="' . $bill->getBoletoName() . '"'
                ]
            );
        }
        return false;
    }

    public function getBoletoFileName(Bill $bill)
    {
        $customerName = Urlizer::urlize($bill->getCustomer()->getName(), '_');
        return $customerName . '_' . $bill->getId() . date('mY') . '.pdf';
    }
}