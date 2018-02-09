<?php

namespace Cps\chequeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $id_servicio = $this->get('security.context')->getToken()->getUser()->getServicio()->getId();
        $user = $this->get('security.context')->getToken()->getUser()->getRoles();
        $rol = array_values($user)[0];
        if ($rol == "ROLE_USER") {
          if ($id_servicio == 6){
              return $this->redirectToRoute('bus_cheque');
          }
          if ($id_servicio != 1) {
  //            return $this->render('CpschequeBundle:Default:index.html.twig');
              return $this->redirectToRoute('movimiento_pendiente');
          }
          else{
              return $this->redirectToRoute('comprobante_index');
          }
        } else {
              return $this->redirectToRoute('bus_nro_cheque');
        }
    }
}
