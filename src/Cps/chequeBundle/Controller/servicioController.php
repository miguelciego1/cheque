<?php

namespace Cps\chequeBundle\Controller;

use Cps\chequeBundle\Entity\servicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Servicio controller.
 *
 * @Route("servicio")
 */
class servicioController extends Controller
{
    /**
     * Lists all servicio entities.
     *
     * @Route("/", name="servicio_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $servicios = $em->getRepository('CpschequeBundle:servicio')->findAll();

        return $this->render('servicio/index.html.twig', array(
            'servicios' => $servicios,
        ));
    }

    /**
     * Creates a new servicio entity.
     *
     * @Route("/new", name="servicio_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $servicio = new Servicio();
        $form = $this->createForm('Cps\chequeBundle\Form\servicioType', $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($servicio);
            $em->flush($servicio);

            return $this->redirectToRoute('servicio_show', array('id' => $servicio->getId()));
        }

        return $this->render('servicio/new.html.twig', array(
            'servicio' => $servicio,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a servicio entity.
     *
     * @Route("/{id}", name="servicio_show")
     * @Method("GET")
     */
    public function showAction(servicio $servicio)
    {
        $deleteForm = $this->createDeleteForm($servicio);

        return $this->render('servicio/show.html.twig', array(
            'servicio' => $servicio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing servicio entity.
     *
     * @Route("/{id}/edit", name="servicio_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, servicio $servicio)
    {
        $deleteForm = $this->createDeleteForm($servicio);
        $editForm = $this->createForm('Cps\chequeBundle\Form\servicioType', $servicio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('servicio_edit', array('id' => $servicio->getId()));
        }

        return $this->render('servicio/edit.html.twig', array(
            'servicio' => $servicio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a servicio entity.
     *
     * @Route("/{id}", name="servicio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, servicio $servicio)
    {
        $form = $this->createDeleteForm($servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($servicio);
            $em->flush($servicio);
        }

        return $this->redirectToRoute('servicio_index');
    }

    /**
     * Creates a form to delete a servicio entity.
     *
     * @param servicio $servicio The servicio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(servicio $servicio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('servicio_delete', array('id' => $servicio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
