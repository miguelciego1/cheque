<?php

namespace Cps\chequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * servicio
 *
 * @ORM\Table(name="servicio")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\servicioRepository")
 */
class servicio
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
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla", type="string", length=10)
     */
    private $abrev;

    /**
     * @var int
     *
     * @ORM\Column(name="prioridad", type="integer")
     */
    private $prioridad;

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
    private $modificado_El;



    public function __toString() {
        return $this->nombre;
    }

    /****************************************************** FORANEA *********************************************************/
    /**
    *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\User", mappedBy="servicio")
    *@ORM\JoinColumn(nullable=true)
    */
     protected $User;
    
    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\movimiento", mappedBy="servicio")
     *@ORM\JoinColumn(nullable=false)
     */
     protected $movimiento;

    /********************************************************************************************************************************/
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return servicio
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
        return $this->nombre;
    }

    /**
     * Set abrev
     *
     * @param string $abrev
     *
     * @return servicio
     */
    public function setAbrev($abrev)
    {
        $this->abrev = $abrev;

        return $this;
    }

    /**
     * Get abrev
     *
     * @return string
     */
    public function getAbrev()
    {
        return $this->abrev;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \Cps\chequeBundle\Entity\User $user
     *
     * @return servicio
     */
    public function addUser(\Cps\chequeBundle\Entity\User $user)
    {
        $this->User[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Cps\chequeBundle\Entity\User $user
     */
    public function removeUser(\Cps\chequeBundle\Entity\User $user)
    {
        $this->User->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->User;
    }

    /**
     * Set permRever
     *
     * @param boolean $permRever
     *
     * @return servicio
     */
    public function setPermRever($permRever)
    {
        $this->permRever = $permRever;

        return $this;
    }

    /**
     * Get permRever
     *
     * @return boolean
     */
    public function getPermRever()
    {
        return $this->permRever;
    }

    /**
     * Add movimiento
     *
     * @param \Cps\chequeBundle\Entity\movimiento $movimiento
     *
     * @return servicio
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
     * Set prioridad
     *
     * @param integer $prioridad
     *
     * @return servicio
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return integer
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set creadoEl
     *
     * @param \DateTime $creadoEl
     *
     * @return servicio
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
     * Set modificadoEl
     *
     * @param \DateTime $modificadoEl
     *
     * @return servicio
     */
    public function setModificadoEl($modificadoEl)
    {
        $this->modificado_El = $modificadoEl;

        return $this;
    }

    /**
     * Get modificadoEl
     *
     * @return \DateTime
     */
    public function getModificadoEl()
    {
        return $this->modificado_El;
    }
}
