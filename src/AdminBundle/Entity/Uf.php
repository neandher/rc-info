<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UfRepository")
 * @ORM\Table(name="uf")
 */
class Uf
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     */
    private $sigla;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $regiao;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     * @return Uf
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * @param string $sigla
     * @return Uf
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegiao()
    {
        return $this->regiao;
    }

    /**
     * @param string $regiao
     * @return Uf
     */
    public function setRegiao($regiao)
    {
        $this->regiao = $regiao;
        return $this;
    }

    public function __toString()
    {
        return $this->sigla . ' - ' . $this->nome;
    }
}