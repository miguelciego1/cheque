<?php

namespace Cps\chequeBundle\Controller;

use Cps\chequeBundle\Entity\benficiario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Benficiario controller.
 *
 * @Route("benficiario")
 */
class benficiarioController extends Controller
{
    /**
     * Lists all benficiario entities.
     *
     * @Route("/", name="benficiario_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $searchQuery = $request->get('query');
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $benficiarios = $em->getRepository('CpschequeBundle:benficiario')->BeneficiarioSearch($searchQuery);
        $paginator=$this->get('knp_paginator');
        $pagination = $paginator->paginate($benficiarios,$request->query->getInt('page',1),11);

        return $this->render('benficiario/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new benficiario entity.
     *
     * @Route("/new", name="benficiario_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $benficiario = new Benficiario();
        $form = $this->createForm('Cps\chequeBundle\Form\benficiarioType', $benficiario);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($benficiario);
            $em->flush($benficiario);
            return $this->redirectToRoute('benficiario_show', array('id' => $benficiario->getId()));
        }
        return $this->render('benficiario/new.html.twig', array(
            'benficiario' => $benficiario,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a benficiario entity.
     *
     * @Route("/{id}", name="benficiario_show")
     * @Method("GET")
     */
    public function showAction(benficiario $benficiario)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('benficiario/show.html.twig', array(
            'benficiario' => $benficiario,
        ));
    }

    /**
     * Displays a form to edit an existing benficiario entity.
     *
     * @Route("/{id}/edit", name="benficiario_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, benficiario $benficiario)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $editForm = $this->createForm('Cps\chequeBundle\Form\benficiarioType', $benficiario);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($benficiario);
            $em->flush($benficiario);
            $this->addFlash('mensaje','El Beneficiario ha sido Modificado.');
            return $this->redirectToRoute('benficiario_edit', array('id' => $benficiario->getId()));
        }
        return $this->render('benficiario/edit.html.twig', array(
            'benficiario' => $benficiario,
            'edit_form' => $editForm->createView(),
        ));
    }
}
