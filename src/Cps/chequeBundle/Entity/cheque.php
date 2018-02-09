<?php
namespace Cps\chequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * cheque
 * 
 * @ORM\Table(name="cheque")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\chequeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class cheque
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var int
     * @Assert\NotBlank(message="Este campo es obligatorio."))
     * @ORM\Column(name="cheque", type="integer")
     */
    private $cheque;

    /**
     * @var string
     * @Assert\NotBlank(message="Este campo es obligatorio."))
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2)
     */
    private $monto;
    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;

    /**
     * @var int
     *
     * @ORM\Column(name="activo", type="integer")
     */
    private $activo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado_el", type="datetime")
     */
    private $creadoEl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modificado_el", type="datetime")
     */
    private $modificadoEl;

    /****************************************************************** CALLBACKS ********************************************************************/
	/**
	 * @ORM\PrePersist
	 */
	public function setCreadoEl()
	{
		$this->creadoEl = new \DateTime();
	}

	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 */
	public function setModificadoEl()
	{
		$this->modificadoEl = new \DateTime();
    }

    /*-------------------------------------------------------------------------------------------------------------------------------------------------*/
    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\comprobante", inversedBy="Cheques")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $comprobante;

    /**
     * 
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\benficiario", inversedBy="Cheques")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $benficiario;

    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\entrega", mappedBy="cheque")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $entrega;

        
    /*-------------------------------------------------------------------------------------------------------------------------------------------------*/

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
     * Set cheque
     *
     * @param integer $cheque
     * @return cheque
     */
    public function setCheque($cheque)
    {
        $this->cheque = $cheque;

        return $this;
    }

    /**
     * Get cheque
     *
     * @return integer 
     */
    public function getCheque()
    {
        return $this->cheque;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return cheque
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return cheque
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
     * Set comprobante
     *
     * @param \Cps\chequeBundle\Entity\comprobante $comprobante
     * @return cheque
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
     * Set benficiario
     *
     * @param \Cps\chequeBundle\Entity\benficiario $benficiario
     * @return cheque
     */
    public function setBenficiario(\Cps\chequeBundle\Entity\benficiario $benficiario)
    {
        $this->benficiario = $benficiario;

        return $this;
    }

    /**
     * Get benficiario
     *
     * @return \Cps\chequeBundle\Entity\benficiario 
     */
    public function getBenficiario()
    {
        return $this->benficiario;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     * @return cheque
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return integer 
     */
    public function getEstado()
    {
        return $this->estado;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movimiento = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add movimiento
     *
     * @param \Cps\chequeBundle\Entity\movimiento $movimiento
     *
     * @return cheque
     */
    public function addMovimiento(\Cps\chequeBundle\Entity\movimiento $movimiento)
    {
        $this->movimiento[] = $movimiento;

        return $this;
    }

    /**
     * Remove movimiento
     *
     * @param \Cps\chequeBundle\Entity\movimiento $movimiento
     */
    public function removeMovimiento(\Cps\chequeBundle\Entity\movimiento $movimiento)
    {
        $this->movimiento->removeElement($movimiento);
    }

    /**
     * Get movimiento
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovimiento()
    {
        return $this->movimiento;
    }

    /**
     * Add entrega
     *
     * @param \Cps\chequeBundle\Entity\entrega $entrega
     *
     * @return cheque
     */
    public function addEntrega(\Cps\chequeBundle\Entity\entrega $entrega)
    {
        $this->entrega[] = $entrega;

        return $this;
    }

    /**
     * Remove entrega
     *
     * @param \Cps\chequeBundle\Entity\entrega $entrega
     */
    public function removeEntrega(\Cps\chequeBundle\Entity\entrega $entrega)
    {
        $this->entrega->removeElement($entrega);
    }

    /**
     * Get entrega
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntrega()
    {
        return $this->entrega;
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
     * Get modificadoEl
     *
     * @return \DateTime
     */
    public function getModificadoEl()
    {
        return $this->modificadoEl;
    }

    /**
     * Set activo
     *
     * @param integer $activo
     *
     * @return cheque
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return integer
     */
    public function getActivo()
    {
        return $this->activo;
    }
}
