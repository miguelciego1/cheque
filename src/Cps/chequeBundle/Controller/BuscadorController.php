<?php

namespace Cps\chequeBundle\Controller;

use Cps\chequeBundle\Entity\cheque;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class BuscadorController extends Controller
{
    public function TotalPendientes($id_servicio){
        $em = $this->getDoctrine()->getManager();
        $pendientes = $em->getRepository('CpschequeBundle:movimiento')->findBy(
            array(
                'servicio' => $id_servicio,
                'recibidoEL' => null,
                'esActivo' => 1,
                'estado' => 0
            )
        );
        return $pendientes;
    }
    /* BUSCAR CHEQUE POR NOMBRE */
    /**
     *
     * @Route("/bus", name="bus")
     * @Method("GET")
     */
    public function busAction(Request $request)
    {
        $id_servicio = $this->get('security.context')->getToken()->getUser()->getServicio()->getId();
        if ($id_servicio == 1){
            $em = $this->getDoctrine()->getManager();
            $nombrebus = $request->get('query');
            if ($nombrebus == null) {
                $nombrebus = $nombrebus . '' . "xxxxx xxxxx xxxxxx";
            }
            $lista = $em->getRepository('CpschequeBundle:Cheque')->buscarNombre($nombrebus);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($lista, $request->query->getInt('page', 1),11);

            return $this->render('buscador/bus.html.twig', array(
                'lista' => $pagination,
            ));
        } else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }
    /* BUSCAR CHEQUE POR NRO*/
    /**
     *
     * @Route("/busnro", name="busnro")
     * @Method("GET")
     */
    public function busnroAction(Request $request)
    {
        $id_servicio = $this->get('security.context')->getToken()->getUser()->getServicio()->getId();
        if ($id_servicio == 1) {
            $nombrebusnro = $request->get('query');
            if ($nombrebusnro == null){
                $nombrebusnro = $nombrebusnro . '' . "123456";
            }

            $em = $this->getDoctrine()->getManager();
            $lista = $em->getRepository('CpschequeBundle:Cheque')->buscarNombreNro($nombrebusnro);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($lista, $request->query->getInt('page', 1),11);

            return $this->render('buscador/busnro.html.twig', array(
                'lista' => $pagination,
            ));
        } else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /* BUSCADOR DE CAJA*/
    /**
     *
     * @Route("/bus/cheque", name="bus_cheque")
     * @Method("GET")
     */
    public function buschequeAction(Request $request)
    {
        $id_servicio = $this->get('security.context')->getToken()->getUser()->getServicio()->getId();
        $pendientes = $this->TotalPendientes($id_servicio);
        $total = count($pendientes);
        $nombre = $request->get('query');
        if ($nombre == null) {
            $nombre = $nombre . '' . "xxxxx xxxxx xxxxxx";
        }
        $em = $this->getDoctrine()->getManager();
        $lista = $em->getRepository('CpschequeBundle:movimiento')->busCheques($nombre);
        $lista = $em->getRepository('CpschequeBundle:Cheque')->buscarNombre($nombre);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($lista, $request->query->getInt('page', 1),11);
        return $this->render('buscador/buscheque.html.twig', array(
            'lista' => $pagination,
            'total' => $total
        ));
    }
    /* BUSCADOR DE CAJA*/
    /**
     *
     * @Route("/bus/nro/cheque", name="bus_nro_cheque")
     * @Method("GET")
     */
    public function busnrochequeAction(Request $request)
    {
        $id_servicio = $this->get('security.context')->getToken()->getUser()->getServicio()->getId();
        $pendientes = $this->TotalPendientes($id_servicio);
        $total = count($pendientes);
        $nombre = $request->get('query');
        if ($nombre == null) {
            $nombre = $nombre . '' . "xxxxx xxxxx xxxxxx";
        }
        $em = $this->getDoctrine()->getManager();
        $lista = $em->getRepository('CpschequeBundle:Cheque')->buscarNombreNro($nombre);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($lista, $request->query->getInt('page', 1),11);
        return $this->render('buscador/busnro.html.twig', array(
            'lista' => $pagination,
            'total' => $total
        ));
    }
}
