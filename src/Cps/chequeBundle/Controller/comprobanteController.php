<?php
namespace Cps\chequeBundle\Controller;

use Cps\chequeBundle\Entity\comprobante;
use Cps\chequeBundle\Entity\User;
use Cps\chequeBundle\Entity\benficiario;
use Cps\chequeBundle\Entity\cheque;
use Cps\chequeBundle\Entity\movimiento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Proxies\__CG__\Cps\chequeBundle\Entity\permiso;


/**
 * Comprobante controller.
 *
 * @Route("comprobante")
 */
class comprobanteController extends Controller
{
    function getUsuario(){
        $usuario = $this->get('security.context')->getToken()->getUser();
        return $usuario;
    }
    function getServicio(){
        $servicio = $this->get('security.context')->getToken()->getUser()->getServicio();
        return $servicio;
    }
    function getPermiso(){
        $permiso = $this->get('security.context')->getToken()->getUser()->getPermiso();
        return $permiso;
    }
    function TotalPendientes(){
        $id_servicio = $this->getServicio()->getId();
        $em = $this->getDoctrine()->getManager();
        $pendientes = $em->getRepository('CpschequeBundle:movimiento')->findBy(
            array(
                'servicio' => $id_servicio,
                'recibidoEL' => null,
                'esActivo' => 1,
                'estado' => 0
            )
        );
        $total = count($pendientes);
        $session = $this->getRequest()->getSession();
        $session->set('total', $total);
    }

    /**
     * Lists all comprobante entities.
     *
     * @Route("/", name="comprobante_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $permiso = $this->getPermiso()->getM1();
        if($permiso){
            $this->TotalPendientes();
        }
        $em = $this->getDoctrine()->getManager();
        $searchQuery = $request->get('query');
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $comprobantes = $em->getRepository('CpschequeBundle:comprobante')->ComprobanteSearch($usuario->getId() , $searchQuery);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($comprobantes, $request->query->getInt('page', 1),11);

        return $this->render('comprobante/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new comprobante entity.
     *
     * @Route("/new", name="comprobante_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $usuario = $this->getUsuario();
        $servicio = $this->getServicio();

        if ($servicio->getId() == 1) {
            $permiso = $this->getPermiso()->getM1();
            if($permiso){$this->TotalPendientes();}

            $comprobante = new Comprobante();
            $form = $this->createForm('Cps\chequeBundle\Form\comprobanteType', $comprobante);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $data = $form->getData();
                $bteData = $data->getbte();
                $result = $em->getRepository('CpschequeBundle:Comprobante')->verificar($bteData);
                $nro = 0;
                foreach ($result as $v) {
                    $nro = $nro + 1;
                }
                if ($nro >= 1) {
                    $this->addFlash('mensaje', 'El número de comprobante ya existe.');
                    return $this->redirect($request->getUri());
                }
                $comprobante->setUsuario($usuario);
                $comprobante->setActivo(1);
                $em->persist($comprobante);
                $em->flush($comprobante);
                if ($nro == 0){
                    $movimiento = new Movimiento();
                    $em = $this->getDoctrine()->getManager();
                    $comprobante_id = $em->getRepository('CpschequeBundle:comprobante')->findOneById($comprobante->getId());
                    $movimiento->setServicio($servicio);
                    $movimiento->setUserRecibido(0);
                    $movimiento->setEstado(0);
                    $movimiento->setUserEnviado(0);
                    $movimiento->setEsActivo(1);
                    $movimiento->setComprobante($comprobante_id);
                    
                    if ($usuario->getId() == 3) {
                      $movimiento->setEstado(1);
                      $movimiento->setRecibidoEL(new \DateTime("now"));
                    }
                    $em->persist($movimiento);
                    $em->flush($movimiento);
                }
                $this->addFlash('mensaje', 'Se ha registrado el comprobante.');
                $session->set('comprobanteId', $comprobante->getId());
                return $this->redirectToRoute('cheque_new');
            }
        }
        else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('comprobante/new.html.twig', array(
            'comprobante' => $comprobante,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a comprobante entity.
     *
     * @Route("/{id}", name="comprobante_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(comprobante $comprobante, Request $request)
    {
        $proId = $comprobante->getId();
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $usuario = $this->getUsuario();
        $servicio = $this->getServicio();

        if ($servicio->getId() == 1) {
            $permiso = $this->getPermiso()->getM1();
            if($permiso){$this->TotalPendientes();}

            $lista = $em->getRepository('CpschequeBundle:comprobante')->Gcheque($proId);
            $total = 0;
            foreach ($lista as $item) {
                $anulado = $item->getEstado();
                if ($anulado < 3 or $anulado == 5)
                    {
                    $montot = $item->getMonto();
                    $total = $total + $montot;
                }
            }
             //----------------------BENEFICIARIO---------------------/
              //formulario de cliente
            $benficiario = new Benficiario();
            $formb = $this->createForm('Cps\chequeBundle\Form\benficiarioType', $benficiario);
            $formb->handleRequest($request);

            //---------------------- CHEQUE---------------------------/
            $cheque = new Cheque();
            $chequeform = array('cheque' => null, 'monto' => null, 'benficiario' => null);
            $form = $this->createFormBuilder($chequeform)
                ->add('cheque')
                ->add('monto')
                ->add('benficiario', 'genemu_jqueryautocomplete_entity', array(
                'class' => 'Cps\chequeBundle\Entity\benficiario',
                'route_name' => 'ajax_benficiario1'
            ))->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comprobanteC = $em->getRepository('CpschequeBundle:comprobante')->find($proId);
                $data = $form->getData();
                $chequeData = $data['cheque'];
                $montoData = $data['monto'];
                $result = $em->getRepository('CpschequeBundle:cheque')->verificar($chequeData);
                $nro = 0;
                foreach ($result as $v) {
                    $nro = $nro + 1;
                }
                if ($nro >= 1) {
                    $this->addFlash('advertencia', 'El número de cheque ya existe.');
                    return $this->redirect($request->getUri());
                }
                $benficiarioid = $em->getRepository('CpschequeBundle:benficiario')->findOneById($data['benficiario']);
                if ($benficiarioid == null && $benficiarioid == 0) {

                    $this->addFlash('advertencia', 'El Beneficiario no existe.');
                    return $this->redirect($request->getUri());
                }
                else {
                    $cheque->setCheque($chequeData);
                    $cheque->setActivo(1);
                    $cheque->setMonto($montoData);
                    $cheque->setComprobante($comprobanteC);
                    $cheque->setBenficiario($benficiarioid);
                    $cheque->setEstado(1);
                    $em->persist($cheque);
                    $em->flush($cheque);

                    return $this->redirect($request->getUri());
                }
            }
            //boton de agrenar beneficiario
            if ($formb->isSubmitted() && $formb->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($benficiario);
                $em->flush($benficiario);
                $this->addFlash('mensaje', 'Se ha registrado el beneficiario correctamente.');

                return $this->redirect($request->getUri());
            }
        }
        else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('comprobante/show.html.twig', array(
            'comprobante' => $comprobante,
            'form' => $form->createView(),
            'formb' => $formb->createView(),
            'total' => $total,
        ));
    }

    /**
     * Displays a form to edit an existing comprobante entity.
     *
     * @Route("/{id}/edit", name="comprobante_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, comprobante $comprobante)
    {
        $session = $this->getRequest()->getSession();
        $usuario = $this->getUsuario();
        $servicio = $this->getServicio();

        if ($servicio->getId() == 1) {
            $permiso = $this->getPermiso()->getM1();
            if($permiso){$this->TotalPendientes();}

            $formEdit = $comprobante->getbte();
            $editForm = $this->createForm('Cps\chequeBundle\Form\comprobanteType', $comprobante);
            $editForm->handleRequest($request);
            $data = $editForm->getData();
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $bteData = $data->getbte();
                if ($bteData == $formEdit) {
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('mensaje', 'El Comprobante se ha Modificado.');
                    return $this->redirectToRoute('comprobante_edit', array('id' => $comprobante->getId()));
                }
                if ($bteData != $formEdit) {
                    $em = $this->getDoctrine()->getManager();
                    $result = $em->getRepository('CpschequeBundle:Comprobante')->verificar($bteData);
                    $nro = 0;
                    foreach ($result as $v) {
                        $nro = $nro + 1;
                    }
                    if ($nro >= 1) {
                        $this->addFlash('advertencia', 'El número de comprobante de ya existe.');
                        return $this->redirect($request->getUri());
                    }
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('mensaje', 'El Comprobante se ha Modificado.');
                    return $this->redirectToRoute('comprobante_edit', array('id' => $comprobante->getId()));
                }
            }
        }
        return $this->render('comprobante/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
        ));
    }
    /**
     * @Route("/ajax/benficiario/", name="ajax_benficiario1")
     */
    public function ajaxAction(Request $request)
    {
        $value = $request->get('term');
        $em = $this->getDoctrine()->getManager();

        $benficiarios = $em->getRepository('CpschequeBundle:benficiario')->findAjaxValue($value);

        $json = array();
        foreach ($benficiarios as $benficiario) {
            $json[] = array(
                'label' => $benficiario->getNombre(),
                'value' => $benficiario->getId() ." ".$benficiario->getNombre()
            );
        }
        $response = new Response(json_encode($json));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
