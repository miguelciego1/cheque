<?php

namespace Cps\chequeBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * movimiento
 *
 * @ORM\Table(name="movimiento")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\movimientoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class movimiento
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
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    private $observacion;
    /**
     * @var int
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado_el", type="datetime")
     */
    private $creadoEl; //equivalente al recibido_El

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recibido_el", type="datetime", nullable=true)
     */
    private $recibidoEL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enviado_el", type="datetime", nullable=true)
     */
     private $enviadoEL;

    /**
     * @var int
     * @ORM\Column(name="u_recibido", type="integer")
     */
    private $userRecibido;

    /**
     * @var int
     * @ORM\Column(name="u_enviado", type="integer")
     */
    private $userEnviado;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
     private $esActivo;
    
    /****************************************************************** CALLBACKS ********************************************************************/    
    /**
    * @ORM\PrePersist
    */
    public function setCreadoElValue()
    {
        $this->creadoEl = new \DateTime();
    }

    /**
    * @ORM\preUpdate
    */
    public function setEnviadoELValue()
    {
        $this->updateAt = new \DateTime();
    }
    /****************************************************** FORANEA *********************************************************/
    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\comprobante", inversedBy="movimiento")
     *@ORM\JoinColumn(nullable=false)
     */
     protected $comprobante;

    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\servicio", inversedBy="movimiento")
     *@ORM\JoinColumn(nullable=false)
     */
     protected $servicio;
    
    /*************************************************************************************************************************/ 
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
     * Set creadoEl
     *
     * @param \DateTime $creadoEl
     *
     * @return movimiento
     */
    public function setCreadoEl($creadoEl)
    {
        $this->creadoEl = $creadoEl;

        return $this;
    }

    /**
     * Get creadoEl
     *
     * @return \DateTime
     */
    public function getCreadoEl()
    {
        return $this->creadoEl;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return movimiento
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set servicio
     *
     * @param \Cps\chequeBundle\Entity\servicio $servicio
     *
     * @return movimiento
     */
    public function setServicio(\Cps\chequeBundle\Entity\servicio $servicio)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \Cps\chequeBundle\Entity\servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return movimiento
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set esActivo
     *
     * @param boolean $esActivo
     *
     * @return movimiento
     */
    public function setEsActivo($esActivo)
    {
        $this->esActivo = $esActivo;

        return $this;
    }

    /**
     * Get esActivo
     *
     * @return boolean
     */
    public function getEsActivo()
    {
        return $this->esActivo;
    }

    /**
     * Set enviadoEL
     *
     * @param \DateTime $enviadoEL
     *
     * @return movimiento
     */
    public function setEnviadoEL($enviadoEL)
    {
        $this->enviadoEL = $enviadoEL;

        return $this;
    }

    /**
     * Get enviadoEL
     *
     * @return \DateTime
     */
    public function getEnviadoEL()
    {
        return $this->enviadoEL;
    }

    /**
     * Set comprobante
     *
     * @param \Cps\chequeBundle\Entity\comprobante $comprobante
     *
     * @return movimiento
     */
    public function setComprobante(\Cps\chequeBundle\Entity\comprobante $comprobante)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \Cps\chequeBundle\Entity\comprobante
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }

    /**
     * Set userRecibido
     *
     * @param integer $userRecibido
     *
     * @return movimiento
     */
    public function setUserRecibido($userRecibido)
    {
        $this->userRecibido = $userRecibido;

        return $this;
    }

    /**
     * Get userRecibido
     *
     * @return integer
     */
    public function getUserRecibido()
    {
        return $this->userRecibido;
    }

    /**
     * Set userEnviado
     *
     * @param integer $userEnviado
     *
     * @return movimiento
     */
    public function setUserEnviado($userEnviado)
    {
        $this->userEnviado = $userEnviado;

        return $this;
    }

    /**
     * Get userEnviado
     *
     * @return integer
     */
    public function getUserEnviado()
    {
        return $this->userEnviado;
    }

    /**
     * Set recibidoEL
     *
     * @param \DateTime $recibidoEL
     *
     * @return movimiento
     */
    public function setRecibidoEL($recibidoEL)
    {
        $this->recibidoEL = $recibidoEL;

        return $this;
    }

    /**
     * Get recibidoEL
     *
     * @return \DateTime
     */
    public function getRecibidoEL()
    {
        return $this->recibidoEL;
    }
}
