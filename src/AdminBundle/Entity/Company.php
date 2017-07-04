<?php

namespace AdminBundle\Entity;

class Company
{
    private $nomeFantasia = 'R C INFORMATICA';
    private $razaoSocial = 'VIRGILANY PERDIGAO ME';
    private $addressStreet = 'AV COR PEDRO MAIA DE CARVALHO';
    private $addressNumber = '425';
    private $postcode = '29.102-570';
    private $city = 'Vila Velha';
    private $uf = 'ES';
    private $cnpj = '04.445.096/0001-52';
    private $agencia = '3132';
    private $conta = '845.2';
    private $conta_digito = '';
    private $codigoBanco = '104';
    private $boletoCarteira = 'RG';
    private $boletoCodigoCliente = '808414-9';
    private $boletoAceite = 'S';
    private $boletoEspecieDoc = 'DM';

    /**
     * @return mixed
     */
    public function getAddressNumber()
    {
        return $this->addressNumber;
    }

    /**
     * @param mixed $addressNumber
     * @return Company
     */
    public function setAddressNumber($addressNumber)
    {
        $this->addressNumber = $addressNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * @param string $addressStreet
     * @return Company
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * @param mixed $agencia
     * @return Company
     */
    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBoletoCarteira()
    {
        return $this->boletoCarteira;
    }

    /**
     * @param mixed $boletoCarteira
     * @return Company
     */
    public function setBoletoCarteira($boletoCarteira)
    {
        $this->boletoCarteira = $boletoCarteira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBoletoCodigoCliente()
    {
        return $this->boletoCodigoCliente;
    }

    /**
     * @param mixed $boletoCodigoCliente
     * @return Company
     */
    public function setBoletoCodigoCliente($boletoCodigoCliente)
    {
        $this->boletoCodigoCliente = $boletoCodigoCliente;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return Company
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param mixed $cnpj
     * @return Company
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    /**
     * @param mixed $codigoBanco
     * @return Company
     */
    public function setCodigoBanco($codigoBanco)
    {
        $this->codigoBanco = $codigoBanco;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * @param mixed $conta
     * @return Company
     */
    public function setConta($conta)
    {
        $this->conta = $conta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContaDigito()
    {
        return $this->conta_digito;
    }

    /**
     * @param mixed $conta_digito
     * @return Company
     */
    public function setContaDigito($conta_digito)
    {
        $this->conta_digito = $conta_digito;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeFantasia()
    {
        return $this->nomeFantasia;
    }

    /**
     * @param string $nomeFantasia
     * @return Company
     */
    public function setNomeFantasia($nomeFantasia)
    {
        $this->nomeFantasia = $nomeFantasia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param mixed $postcode
     * @return Company
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getRazaoSocial()
    {
        return $this->razaoSocial;
    }

    /**
     * @param string $razaoSocial
     * @return Company
     */
    public function setRazaoSocial($razaoSocial)
    {
        $this->razaoSocial = $razaoSocial;
        return $this;
    }

    /**
     * @return string
     */
    public function getBoletoAceite()
    {
        return $this->boletoAceite;
    }

    /**
     * @param string $boletoAceite
     * @return Company
     */
    public function setBoletoAceite($boletoAceite)
    {
        $this->boletoAceite = $boletoAceite;
        return $this;
    }

    /**
     * @return string
     */
    public function getBoletoEspecieDoc()
    {
        return $this->boletoEspecieDoc;
    }

    /**
     * @param string $boletoEspecieDoc
     * @return Company
     */
    public function setBoletoEspecieDoc($boletoEspecieDoc)
    {
        $this->boletoEspecieDoc = $boletoEspecieDoc;
        return $this;
    }

    /**
     * @return string
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param string $uf
     * @return Company
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
        return $this;
    }
}