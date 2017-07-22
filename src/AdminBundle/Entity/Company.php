<?php

namespace AdminBundle\Entity;

use AppBundle\Resource\Model\AddressTrait;
use AppBundle\Resource\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\CompanyRepository")
 * @ORM\Table(name="company")
 */
class Company
{
    /*private $nomeFantasia = 'R C INFORMATICA';
    private $razaoSocial = 'VIRGILANY PERDIGAO ME';
    private $addressStreet = 'AV COR PEDRO MAIA DE CARVALHO';
    private $addressNumber = '425';
    private $zipCode = '29102-570';
    private $city = 'Vila Velha';
    private $district = 'Praia das Gaivotas';
    private $uf = 'ES';
    private $cnpj = '04.445.096/0001-52';
    private $agencia = '3132';
    private $agenciaDigito = '1';
    private $conta = '845';
    private $contaDigito = '2';
    private $codigoBanco = '104';
    private $carteira = 'RG';
    private $codigoCliente = '808414-9';
    private $aceite = 'S';
    private $especieDoc = 'DM';
    private $juros = '0,81';
    private $multa = '4,94';
    private $prazoAposVencimento = 120;
    private $codigoProtesto = 1; // 1 = Protestar com (Prazo) dias, 3 = Devolver apos (Prazo) dias
    private $prazoProtesto = 5; // Informar o numero de dias apos o vencimento para iniciar o protesto
    private $codigoBaixaDevolucao = 1; // codigo para indicar o tipo de baixa '1' (Baixar/ Devolver) ou '2' (Nao Baixar / Nao Devolver)
    private $prazoBaixaDevolucao = 120; // prazo de dias para o cliente pagar apos o vencimento*/

    use AddressTrait, TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $nomeFantasia;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $razaoSocial;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $cnpj;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $agencia;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $agenciaDigito;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $conta;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $contaDigito;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $codigoBanco;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $carteira;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $codigoCliente;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $aceite;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $especieDoc;

    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     * @Assert\LessThan(value="99999999.99")
     */
    private $juros;

    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     * @Assert\LessThan(value="99999999.99")
     */
    private $multa;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $prazoAposVencimento;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $codigoProtesto;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $prazoProtesto;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $codigoBaixaDevolucao;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $prazoBaixaDevolucao;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nomeFantasia
     *
     * @param string $nomeFantasia
     *
     * @return Company
     */
    public function setNomeFantasia($nomeFantasia)
    {
        $this->nomeFantasia = $nomeFantasia;

        return $this;
    }

    /**
     * Get nomeFantasia
     *
     * @return string
     */
    public function getNomeFantasia()
    {
        return $this->nomeFantasia;
    }

    /**
     * Set razaoSocial
     *
     * @param string $razaoSocial
     *
     * @return Company
     */
    public function setRazaoSocial($razaoSocial)
    {
        $this->razaoSocial = $razaoSocial;

        return $this;
    }

    /**
     * Get razaoSocial
     *
     * @return string
     */
    public function getRazaoSocial()
    {
        return $this->razaoSocial;
    }

    /**
     * Set cnpj
     *
     * @param string $cnpj
     *
     * @return Company
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    /**
     * Get cnpj
     *
     * @return string
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * Set agencia
     *
     * @param string $agencia
     *
     * @return Company
     */
    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;

        return $this;
    }

    /**
     * Get agencia
     *
     * @return string
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Set agenciaDigito
     *
     * @param string $agenciaDigito
     *
     * @return Company
     */
    public function setAgenciaDigito($agenciaDigito)
    {
        $this->agenciaDigito = $agenciaDigito;

        return $this;
    }

    /**
     * Get agenciaDigito
     *
     * @return string
     */
    public function getAgenciaDigito()
    {
        return $this->agenciaDigito;
    }

    /**
     * Set conta
     *
     * @param string $conta
     *
     * @return Company
     */
    public function setConta($conta)
    {
        $this->conta = $conta;

        return $this;
    }

    /**
     * Get conta
     *
     * @return string
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * Set contaDigito
     *
     * @param string $contaDigito
     *
     * @return Company
     */
    public function setContaDigito($contaDigito)
    {
        $this->contaDigito = $contaDigito;

        return $this;
    }

    /**
     * Get contaDigito
     *
     * @return string
     */
    public function getContaDigito()
    {
        return $this->contaDigito;
    }

    /**
     * Set codigoBanco
     *
     * @param string $codigoBanco
     *
     * @return Company
     */
    public function setCodigoBanco($codigoBanco)
    {
        $this->codigoBanco = $codigoBanco;

        return $this;
    }

    /**
     * Get codigoBanco
     *
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    /**
     * Set carteira
     *
     * @param string $carteira
     *
     * @return Company
     */
    public function setCarteira($carteira)
    {
        $this->carteira = $carteira;

        return $this;
    }

    /**
     * Get carteira
     *
     * @return string
     */
    public function getCarteira()
    {
        return $this->carteira;
    }

    /**
     * Set codigoCliente
     *
     * @param string $codigoCliente
     *
     * @return Company
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * Get codigoCliente
     *
     * @return string
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Set aceite
     *
     * @param string $aceite
     *
     * @return Company
     */
    public function setAceite($aceite)
    {
        $this->aceite = $aceite;

        return $this;
    }

    /**
     * Get aceite
     *
     * @return string
     */
    public function getAceite()
    {
        return $this->aceite;
    }

    /**
     * Set especieDoc
     *
     * @param string $especieDoc
     *
     * @return Company
     */
    public function setEspecieDoc($especieDoc)
    {
        $this->especieDoc = $especieDoc;

        return $this;
    }

    /**
     * Get especieDoc
     *
     * @return string
     */
    public function getEspecieDoc()
    {
        return $this->especieDoc;
    }

    /**
     * Set juros
     *
     * @param string $juros
     *
     * @return Company
     */
    public function setJuros($juros)
    {
        $this->juros = $juros;

        return $this;
    }

    /**
     * Get juros
     *
     * @return string
     */
    public function getJuros()
    {
        return $this->juros;
    }

    /**
     * Set multa
     *
     * @param string $multa
     *
     * @return Company
     */
    public function setMulta($multa)
    {
        $this->multa = $multa;

        return $this;
    }

    /**
     * Get multa
     *
     * @return string
     */
    public function getMulta()
    {
        return $this->multa;
    }

    /**
     * Set prazoAposVencimento
     *
     * @param string $prazoAposVencimento
     *
     * @return Company
     */
    public function setPrazoAposVencimento($prazoAposVencimento)
    {
        $this->prazoAposVencimento = $prazoAposVencimento;

        return $this;
    }

    /**
     * Get prazoAposVencimento
     *
     * @return string
     */
    public function getPrazoAposVencimento()
    {
        return $this->prazoAposVencimento;
    }

    /**
     * Set codigoProtesto
     *
     * @param string $codigoProtesto
     *
     * @return Company
     */
    public function setCodigoProtesto($codigoProtesto)
    {
        $this->codigoProtesto = $codigoProtesto;

        return $this;
    }

    /**
     * Get codigoProtesto
     *
     * @return string
     */
    public function getCodigoProtesto()
    {
        return $this->codigoProtesto;
    }

    /**
     * Set prazoProtesto
     *
     * @param string $prazoProtesto
     *
     * @return Company
     */
    public function setPrazoProtesto($prazoProtesto)
    {
        $this->prazoProtesto = $prazoProtesto;

        return $this;
    }

    /**
     * Get prazoProtesto
     *
     * @return string
     */
    public function getPrazoProtesto()
    {
        return $this->prazoProtesto;
    }

    /**
     * Set codigoBaixaDevolucao
     *
     * @param string $codigoBaixaDevolucao
     *
     * @return Company
     */
    public function setCodigoBaixaDevolucao($codigoBaixaDevolucao)
    {
        $this->codigoBaixaDevolucao = $codigoBaixaDevolucao;

        return $this;
    }

    /**
     * Get codigoBaixaDevolucao
     *
     * @return string
     */
    public function getCodigoBaixaDevolucao()
    {
        return $this->codigoBaixaDevolucao;
    }

    /**
     * Set prazoBaixaDevolucao
     *
     * @param string $prazoBaixaDevolucao
     *
     * @return Company
     */
    public function setPrazoBaixaDevolucao($prazoBaixaDevolucao)
    {
        $this->prazoBaixaDevolucao = $prazoBaixaDevolucao;

        return $this;
    }

    /**
     * Get prazoBaixaDevolucao
     *
     * @return string
     */
    public function getPrazoBaixaDevolucao()
    {
        return $this->prazoBaixaDevolucao;
    }
}
