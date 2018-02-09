<?php

namespace Cps\chequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * benficiario
 * @UniqueEntity("nitci")
 * @ORM\Table(name="benficiario")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\benficiarioRepository")
 */
class benficiario
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Debe escribir el nombre del beneficiario"))
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string", length=100, nullable=true)
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=255 , nullable=true)
     */
    private $correo;

    /**
     * @var string
     * @Assert\NotBlank(message="Debe ingresar el C.I o NIT del Beneficiario."))
     * @ORM\Column(name="nitci", type="string", length=11)
     */
    private $nitci;


    /*--------------------------------------------------------------------------------------------------------------------------*/  
    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\cheque", mappedBy="benficiario")
     *@ORM\JoinColumn(nullable=false)
     */
     protected $cheque;
    /*--------------------------------------------------------------------------------------------------------------------------*/
    public function __toString(){
      	return $this->nombre;
    }
     

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
     * Set nombre
     *
     * @param string $nombre
     * @return benficiario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return (strtoupper($this->nombre));
    }

    /**
     * Set empresa
     *
     * @param string $empresa
     * @return benficiario
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return string 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set correo
     *
     * @param string $correo
     * @return benficiario
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string 
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set nitci
     *
     * @param string $nitci
     * @return benficiario
     */
    public function setNitci($nitci)
    {
        $this->nitci = $nitci;

        return $this;
    }

    /**
     * Get nitci
     *
     * @return string 
     */
    public function getNitci()
    {
        return $this->nitci;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cheque = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cheque
     *
     * @param \Cps\chequeBundle\Entity\cheque $cheque
     * @return benficiario
     */
    public function addCheque(\Cps\chequeBundle\Entity\cheque $cheque)
    {
        $this->cheque[] = $cheque;

        return $this;
    }

    /**
     * Remove cheque
     *
     * @param \Cps\chequeBundle\Entity\cheque $cheque
     */
    public function removeCheque(\Cps\chequeBundle\Entity\cheque $cheque)
    {
        $this->cheque->removeElement($cheque);
    }

    /**
     * Get cheque
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCheque()
    {
        return $this->cheque;
    }
}
