<?php

namespace Cps\chequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * permiso
 *
 * @ORM\Table(name="permiso")
 * @ORM\Entity(repositoryClass="Cps\chequeBundle\Repository\permisoRepository")
 */
class permiso
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
     * @ORM\Column(name="glosa", type="text", nullable=true)
     */
     private $glosa;

    /**
     * @var bool
     *
     * @ORM\Column(name="e1", type="boolean")
     */
     private $e1;

    /**
     * @var bool
     *
     * @ORM\Column(name="r1", type="boolean")
     */
     private $r1;

    /**
     * @var bool
     *
     * @ORM\Column(name="m1", type="boolean")
     */
     private $m1;
    /**
     * @var bool
     *
     * @ORM\Column(name="b1", type="boolean")
     */
    private $b1;

    /****************************************************** FORANEA *********************************************************/
    /**
    *@ORM\OneToMany(targetEntity="Cps\chequeBundle\Entity\User", mappedBy="permiso")
    *@ORM\JoinColumn(nullable=true)
    */
    protected $User;    
    
    /*************************************************************************************************************************/

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
     * Set permisoDe
     *
     * @param integer $permisoDe
     *
     * @return permiso
     */
    public function setPermisoDe($permisoDe)
    {
        $this->permisoDe = $permisoDe;

        return $this;
    }

    /**
     * Get permisoDe
     *
     * @return int
     */
    public function getPermisoDe()
    {
        return $this->permisoDe;
    }

    /**
     * Set glosa
     *
     * @param string $glosa
     *
     * @return permiso
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
     * Constructor
     */
    public function __construct()
    {
        $this->permiso = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add permiso
     *
     * @param \Cps\chequeBundle\Entity\User $permiso
     *
     * @return permiso
     */
    public function addPermiso(\Cps\chequeBundle\Entity\User $permiso)
    {
        $this->permiso[] = $permiso;

        return $this;
    }

    /**
     * Remove permiso
     *
     * @param \Cps\chequeBundle\Entity\User $permiso
     */
    public function removePermiso(\Cps\chequeBundle\Entity\User $permiso)
    {
        $this->permiso->removeElement($permiso);
    }

    /**
     * Get permiso
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermiso()
    {
        return $this->permiso;
    }
     
    /**
     * Set e1
     *
     * @param boolean $e1
     *
     * @return permiso
     */
    public function setE1($e1)
    {
        $this->e1 = $e1;

        return $this;
    }

    /**
     * Get e1
     *
     * @return boolean
     */
    public function getE1()
    {
        return $this->e1;
    }

    /**
     * Set r1
     *
     * @param boolean $r1
     *
     * @return permiso
     */
    public function setR1($r1)
    {
        $this->r1 = $r1;

        return $this;
    }

    /**
     * Get r1
     *
     * @return boolean
     */
    public function getR1()
    {
        return $this->r1;
    }

    /**
     * Add user
     *
     * @param \Cps\chequeBundle\Entity\User $user
     *
     * @return permiso
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
     * Set m1
     *
     * @param boolean $m1
     *
     * @return permiso
     */
    public function setM1($m1)
    {
        $this->m1 = $m1;

        return $this;
    }

    /**
     * Get m1
     *
     * @return boolean
     */
    public function getM1()
    {
        return $this->m1;
    }

    /**
     * Set b1
     *
     * @param boolean $b1
     *
     * @return permiso
     */
    public function setB1($b1)
    {
        $this->b1 = $b1;

        return $this;
    }

    /**
     * Get b1
     *
     * @return boolean
     */
    public function getB1()
    {
        return $this->b1;
    }
}
