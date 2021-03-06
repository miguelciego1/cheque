<?php
namespace Cps\chequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * comprobante
 *
 * @ORM\Table(name="comprobante")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\comprobanteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class comprobante
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
     * @Assert\NotBlank(message="Debe ingresar el número de comprobante."))
     * @ORM\Column(name="bte", type="string", length=10)
     * @Assert\Length(
     *      min = 6,
     *      max = 6,
     *      minMessage = " minino {{ limit }} characters long",
     *      maxMessage = "maximo {{ limit }} characters"
     * )
     */
    private $bte;

    /**
     * @var string
     * @Assert\NotBlank(message="Debe Ingresar el Documento de Respaldo."))
     * @ORM\Column(name="docres", type="string", length=15)
     */
    private $docres;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Debe ingresar la fecha."))
     * @Assert\DateTime( message="Formato de fecha no valido.")
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     * @ORM\Column(name="glosa", type="text", nullable=true)
     */
    private $glosa;

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
    
    /*--------------------------------------------------------FORANEAS----------------------------------------------------------*/
    /**
     * 
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\User", inversedBy="comprobante")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $Usuario;

    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\cheque", mappedBy="comprobante")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $cheque;

    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\movimiento", mappedBy="comprobante")
     *@ORM\JoinColumn(nullable=false)
     */
    protected $movimiento;

    /*--------------------------------------------------------------------------------------------------------------------------*/
    public function __toString()
    {
        return $this->bte;
    }
    /*--------------------------------------------------------------------------------------------------------------------------*/
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
     * Set bte
     *
     * @param string $bte
     * @return comprobante
     */
    public function setBte($bte)
    {
        $this->bte = $bte;

        return $this;
    }

    /**
     * Get bte
     *
     * @return string 
     */
    public function getBte()
    {
        return $this->bte;
    }

    /**
     * Set docres
     *
     * @param string $docres
     * @return comprobante
     */
    public function setDocres($docres)
    {
        $this->docres = $docres;

        return $this;
    }

    /**
     * Get docres
     *
     * @return string 
     */
    public function getDocres()
    {
        return $this->docres;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return comprobante
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
     * Set glosa
     *
     * @param string $glosa
     * @return comprobante
     */
    public function setGlosa($glosa)
    {
        $this->glosa = $glosa;

        return $this;
    }

    /**
     * Get glosa
     *
     * @return string 
     */
    public function getGlosa()
    {
        return $this->glosa;
    }

    /**
     * Set Usuario
     *
     * @param \Cps\chequeBundle\Entity\User $usuario
     * @return comprobante
     */
    public function setUsuario(\Cps\chequeBundle\Entity\User $usuario)
    {
        $this->Usuario = $usuario;

        return $this;
    }

    /**
     * Get Usuario
     *
     * @return \Cps\chequeBundle\Entity\User 
     */
    public function getUsuario()
    {
        return $this->Usuario;
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
     * @return comprobante
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

    /**
     * Add movimiento
     *
     * @param \Cps\chequeBundle\Entity\movimiento $movimiento
     *
     * @return comprobante
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
     * @return comprobante
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
