<?php
// src/AppBundle/Entity/User.php

namespace Cps\chequeBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /*--------------------------------------------------------FORANEAS----------------------------------------------------------*/
    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\comprobante", mappedBy="User")
     */
     protected $comprobante;

    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\servicio", inversedBy="User")
     *@ORM\JoinColumn(nullable=true)
     */
     protected $servicio;
    
    /**
     *@ORM\ManyToOne(targetEntity="Cps\chequeBundle\Entity\permiso", inversedBy="User")
     *@ORM\JoinColumn(nullable=true)
     */
     protected $permiso;

    /**
     *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\entrega", mappedBy="User")
     */
     protected $entrega;

    /*--------------------------------------------------------/FORANEAS----------------------------------------------------------*/
    

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add comprobante
     *
     * @param \Cps\chequeBundle\Entity\comprobante $comprobante
     *
     * @return User
     */
    public function addComprobante(\Cps\chequeBundle\Entity\comprobante $comprobante)
    {
        $this->comprobante[] = $comprobante;

        return $this;
    }

    /**
     * Remove comprobante
     *
     * @param \Cps\chequeBundle\Entity\comprobante $comprobante
     */
    public function removeComprobante(\Cps\chequeBundle\Entity\comprobante $comprobante)
    {
        $this->comprobante->removeElement($comprobante);
    }

    /**
     * Get comprobante
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }

    /**
     * Set servicio
     *
     * @param \Cps\chequeBundle\Entity\servicio $servicio
     *
     * @return User
     */
    public function setServicio(\Cps\chequeBundle\Entity\servicio $servicio = null)
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
     * Set permiso
     *
     * @param \Cps\chequeBundle\Entity\permiso $permiso
     *
     * @return User
     */
    public function setPermiso(\Cps\chequeBundle\Entity\permiso $permiso = null)
    {
        $this->permiso = $permiso;

        return $this;
    }

    /**
     * Get permiso
     *
     * @return \Cps\chequeBundle\Entity\permiso
     */
    public function getPermiso()
    {
        return $this->permiso;
    }

    /**
     * Add entrega
     *
     * @param \Cps\chequeBundle\Entity\entrega $entrega
     *
     * @return User
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
}
