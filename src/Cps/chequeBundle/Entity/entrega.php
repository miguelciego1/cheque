<?php

namespace Cps\chequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * entrega
 *
 * @ORM\Table(name="cheque_entrega")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\entregaRepository")
 */
class entrega
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="esActivo", type="integer")
     */
    private $esActivo;

    /*--------------------------------------------------------FORANEAS----------------------------------------------------------*/
    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\cheque", inversedBy="entrega")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $cheque;
    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\User", inversedBy="entrega")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $usuario;
    /*-------------------------------------------------------- FIN FORANEAS----------------------------------------------------------*/
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set esActivo
     *
     * @param integer $esActivo
     *
     * @return entrega
     */
    public function setEsActivo($esActivo)
    {
        $this->esActivo = $esActivo;

        return $this;
    }

    /**
     * Get esActivo
     *
     * @return int
     */
    public function getEsActivo()
    {
        return $this->esActivo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return entrega
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set cheque
     *
     * @param \Cps\chequeBundle\Entity\cheque $cheque
     *
     * @return entrega
     */
    public function setCheque(\Cps\chequeBundle\Entity\cheque $cheque)
    {
        $this->cheque = $cheque;

        return $this;
    }

    /**
     * Get cheque
     *
     * @return \Cps\chequeBundle\Entity\cheque
     */
    public function getCheque()
    {
        return $this->cheque;
    }

    /**
     * Set usuario
     *
     * @param \Cps\chequeBundle\Entity\User $usuario
     *
     * @return entrega
     */
    public function setUsuario(\Cps\chequeBundle\Entity\User $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Cps\chequeBundle\Entity\User
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
