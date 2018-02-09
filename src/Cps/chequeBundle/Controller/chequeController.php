<?php
namespace Cps\chequeBundle\Controller;

use Cps\chequeBundle\Entity\cheque;
use Cps\chequeBundle\Entity\comprobante;
use Cps\chequeBundle\Entity\benficiario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;


use Cps\chequeBundle\Libreria\numeroALetraConvertidor;
use Cps\chequeBundle\Libreria\fechaALetraConvertidor;



/**
 * Cheque controller.
 *
 * @Route("cheque")
 */
class chequeController extends Controller
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
     * Lists all cheque entities.
     *
     * @Route("/", name="cheque_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $usuario = $this->getUsuario();
        $servicio = $this->getServicio();

        if ($servicio->getId() == 1) {
            $permiso = $this->getPermiso()->getM1();
            if($permiso){$this->TotalPendientes();}

            $em = $this->getDoctrine()->getManager();
            $searchQuery = $request->get('query');
            if ($searchQuery == null)
                {
                $searchQuery = $searchQuery . '' . "123456789000";
            }
            $cheques = $em->getRepository('CpschequeBundle:Cheque')->ChequeSearch($usuario->getId(), $searchQuery);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate( $cheques, $request->query->getInt('page', 1), 11);
        } else {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('cheque/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }


    /**
     * Lists all cheque entities.
     *
     * @Route("/anulado", name="cheque_anulado")
     * @Method("GET")
     */
    public function anuladoAction(Request $request)
    {
        $usuario = $this->getUsuario();
        $servicio = $this->getServicio();

        if ($servicio->getId() == 1) {
            $permiso = $this->getPermiso()->getM1();
            if($permiso){$this->TotalPendientes();}

            $searchQuery = $request->get('query');

            $em = $this->getDoctrine()->getManager();
            $cheques = $em->getRepository('CpschequeBundle:Cheque')->aChequeSearch($usuario->getId(), $searchQuery);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($cheques,$request->query->getInt('page', 1), 11);

            return $this->render('cheque/anulado.html.twig', array(
                'pagination' => $pagination,
            ));

        } else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/anular/{id}", name="cheque_anular")
     * @Method("GET")
     */
    public function anularAction(Request $request, cheque $cheque)
    {
        $servicio = $this->getServicio();
        $usuario = $this->getUsuario();
        if ($servicio->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $cheque->setEstado(3);
            $em->persist($cheque);
            $flush = $em->flush();

            $chequem = $cheque->getCheque();
            $ben = $cheque->getBenficiario();
            $this->addFlash('mensaje', 'El Cheque ' . $chequem . ' del beneficiario ' . $ben . ' ha sido Anulado correctamente.');

            $searchQuery = $request->get('query');
            $cheques = $em->getRepository('CpschequeBundle:Cheque')->aChequeSearch($usuario->getId(), $searchQuery);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate( $cheques, $request->query->getInt('page', 1), 11 );

            return $this->render('cheque/anulado.html.twig', array(
                'pagination' => $pagination,
            ));
        }
    }

    /*REVERCION*/
    /**
     * Lists all cheque entities.
     *
     * @Route("/revertido", name="cheque_revertido")
     * @Method("GET")
     */
    public function preRevertirAction(Request $request)
    {
        $searchQuery = $request->get('query');
        if ($searchQuery == null) {
            $searchQuery = $searchQuery . '' . "123456789000";
        }
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        if ($usuario->getPermiso()->getR1() == 1) {
            $em = $this->getDoctrine()->getManager();
            $cheques = $em->getRepository('CpschequeBundle:Cheque')->rChequeSearch($searchQuery);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate( $cheques, $request->query->getInt('page', 1), 11);

            return $this->render('cheque/revertir.html.twig', array(
                'pagination' => $pagination,
            ));
        }
        else {
            $this->addFlash('advertencia', 'Lo sentimos, Pero no tienes permiso para revertir cheques.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/revertir/{id}", name="cheque_revertir")
     * @Method("GET")
     */
    public function revertirAction(Request $request, cheque $cheque)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }

        if ($usuario->getPermiso()->getR1() == 1) {
            $cheque->setEstado(4);
            $em = $this->getDoctrine()->getManager();
            $em->persist($cheque);
            $flush = $em->flush();

            $chequem = $cheque->getCheque();
            $ben = $cheque->getBenficiario();
            $this->addFlash('mensaje', 'El Cheque ' . $chequem . ' del beneficiario ' . $ben . ' ha sido revertido correctamente.');

            $searchQuery = $request->get('query');
            $cheques = $em->getRepository('CpschequeBundle:Cheque')->rCheque($searchQuery);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate( $cheques, $request->query->getInt('page', 1), 11 );
            return $this->render('cheque/revertido.html.twig', array(
                'pagination' => $pagination,
            ));
        }
        else {
            return $this->redirectToRoute('homepage');
        }
    }

    /* REPORTE DE CHEQUE POR USUARIO*/
    /**
     * Lists all cheque entities.
     *
     * @Route("/reporte/usuario", name="cheque_reporte_usuario")
     * @Method("GET")
     */
    public function reporteUsuarioAction(Request $request)
    {
        $asd = "hola";
        //dump($asd);die;
        $fini = $request->get('fini');
        $ffin = $request->get('ffin');

        $em = $this->getDoctrine()->getManager();

        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $lista = $em->getRepository('CpschequeBundle:Cheque')->reportePorUsuario($fini, $ffin, $usuario);



        /* TOTAL DE CHEQUES*/
        $cheque_noImpreso = 0;
        $cheque_Impreso = 0;
        $cheque_anulado = 0;
        $cheque_revertido = 0;
        $cheque_entregado = 0;
        $totales = 0;
        foreach ($lista as $item) {

            $est = $item['estado'];
            if ($est == 1) {
                $cheque_noImpreso++;
            }
            if ($est == 2) {
                $cheque_Impreso++;
            }
            if ($est == 3) {
                $cheque_anulado++;
            }
            if ($est == 4) {
                $cheque_revertido++;
            }
            if ($est == 5) {
                $cheque_entregado++;
            }
            $totales++;
        }
        /*---------------------------*/
        $obtenerfecha = new fechaAletraConvertidor();
        $fecha = $obtenerfecha->obtenermes($fini);

        return $this->render('cheque/reportUser.html.twig', array(
            'lista' => $lista,
            'fecha' => $fecha,
            'noimpreso' => $cheque_noImpreso,
            'impreso' => $cheque_Impreso,
            'anulado' => $cheque_anulado,
            'revertido' => $cheque_revertido,
            'entregado' => $cheque_entregado,
            'totales' => $totales,
        ));
    }

    /**
     * Lists all cheque entities.
     *
     * @Route("/reporte", name="cheque_reporte")
     * @Method("GET")
     */
    public function reporteAction(Request $request)
    {
        $servicio = $this->getServicio();
        if ($servicio->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }

        $fini = $request->get('fini');
        $ffin = $request->get('ffin');

        $em = $this->getDoctrine()->getManager();
        $lista = $em->getRepository('CpschequeBundle:Cheque')->reportegeneral($fini, $ffin);
        /* TOTAL DE CHEQUES*/
        $cheque_noImpreso = 0;
        $cheque_Impreso = 0;
        $cheque_anulado = 0;
        $cheque_revertido = 0;
        $cheque_entregado = 0;
        $totales = 0;
        foreach ($lista as $item) {
            $est = $item['estado'];
            if ($est == 1) {
                $cheque_noImpreso++;
            }
            if ($est == 2) {
                $cheque_Impreso++;
            }
            if ($est == 3) {
                $cheque_anulado++;
            }
            if ($est == 4) {
                $cheque_revertido++;
            }
            if ($est == 5) {
                $cheque_entregado++;
            }
            $totales++;
        }
        /*---------------------------*/
        $obtenerfecha = new fechaAletraConvertidor();
        $fecha = $obtenerfecha->obtenermes($fini);


        return $this->render('cheque/reporte.html.twig', array(
            'lista' => $lista,
            'fecha' => $fecha,
            'noimpreso' => $cheque_noImpreso,
            'impreso' => $cheque_Impreso,
            'anulado' => $cheque_anulado,
            'revertido' => $cheque_revertido,
            'entregado' => $cheque_entregado,
            'totales' => $totales,
        ));
    }

    /*EXPORTAR A EXCEL*/

    /**
     * Lists all cheque entities.
     *
     * @Route("/Exportar/Excel", name="cheque_reporte_excel")
     * @Method({"GET", "POST"})
     */
    public function reporteExcelAction(Request $request)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1 ) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        if ($usuario->getPermiso()->getR1() == 1) {
            $excelform = array('fechaini' => null, 'fechafin' => null);
            $form = $this->createFormBuilder($excelform)
                ->add('fechaini','date',array(
                    'widget' => 'single_text'))
                ->add('fechafin','date',array(
                    'widget' => 'single_text'))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $fini = $data['fechaini'];
                $ffin = $data['fechafin'];
                $em = $this->getDoctrine()->getManager();
                $cheques = $em->getRepository('CpschequeBundle:Comprobante')->datoExcel($fini, $ffin);

                //dump($cheques);die;

                $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

                $phpExcelObject->getProperties()
                    ->setCreator("Vabadus")
                    ->setLastModifiedBy("Vabadus")
                    ->setTitle("Reporte de Cheque")
                    ->setSubject("Excel Cheque")
                    ->setDescription("Listado.")
                    ->setKeywords("vabadus exportar excel ejemplo");

            //fecha literal
                $mesliteral = new fechaAletraConvertidor();

                $getDate = $fini->format('d-m-Y');
                $fecha = $mesliteral->obtenermes($getDate);

            // establecemos como hoja activa la primera, y le asignamos un t�tulo
                $phpExcelObject->setActiveSheetIndex(0);
                $phpExcelObject->getActiveSheet()->setTitle('' . $fecha . '');

            // escribimos en distintas celdas del documento el t�tulo de los campos que vamos a exportar
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B2', 'FECHA')
                    ->setCellValue('C2', 'NOMBRE EMPRESA')
                    ->setCellValue('D2', 'CONCEPTO')
                    ->setCellValue('E2', 'NRO CHEQUE')
                    ->setCellValue('F2', 'MONTO')
                    ->setCellValue('G2', 'NRO VAL')
                    ->setCellValue('H2', 'NRO CP')
                    ->setCellValue('I2', 'ESTADO');

        // fijamos un ancho a las distintas columnas
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(45);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(50);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(25);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(20);
                $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);

        //recorremos los registros obtenidos de la consulta a base de datos escribi�ndolos en las celdas correspondientes
                $row = 3;

                foreach ($cheques as $item) {
                    $fechaxl = $item['fecha'];
                    $xlsxfecha = $fechaxl->format('d/m/Y');
                    $estado = $item['estado'];
                    switch ($estado) {
                        case $estado == 1:
                            $estado  = "No impreso";
                            break;
                        case $estado == 2 or $estado == 5:
                            $estado  = "Impreso";
                            break;
                        case $estado == 3:
                            $estado  = "Anulado";
                            break;
                        case $estado == 4:
                            $estado  = "Revertido";
                            break;
                        default:
                            $estado  = "Default";
                            break;
                    }
                  
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('B' . $row, $xlsxfecha)
                        ->setCellValue('C' . $row, strtoupper ($item['nombre']))
                        ->setCellValue('D' . $row, $item['glosa'])
                        ->setCellValue('E' . $row, $item['cheque'])

                        ->setCellValue('F' . $row, $item['monto'])
                        ->setCellValue('G' . $row, $item['bte'])
                        ->setCellValue('H' . $row, $item['docres'])
                        ->setCellValue('I' . $row, $estado);

                    $row++;
                }
        // se crea el writer
                $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
                $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por �ltimo se a�aden las cabeceras
                $dispositionHeader = $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    'Egreso de banco ' . $fecha . '.xls'
                );
                $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
                $response->headers->set('Pragma', 'public');
                $response->headers->set('Cache-Control', 'maxage=1');
                $response->headers->set('Content-Disposition', $dispositionHeader);
                return $response;
            }
            return $this->render('cheque/Excel.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        else {
            $this->addFlash('advertencia', 'Lo sentimos, Pero tienes permiso para generar reportes en Excel.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Creates a new cheque entity.
     *
     * @Route("/new", name="cheque_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cheque = new Cheque();
        $session = $this->getRequest()->getSession(); //trae session
        $comprobanteId = $session->get('comprobanteId'); //cabecera
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }

        $comprobanteC = $em->getRepository('CpschequeBundle:comprobante')->find($comprobanteId);//obteniendo el registro de la llave primaria
        //formulario de cliente
        $benficiario = new Benficiario();
        $formb = $this->createForm('Cps\chequeBundle\Form\benficiarioType', $benficiario);
        $formb->handleRequest($request);

        // crear nuevo formulario de cheque
        $chequeform = array('cheque' => null, 'monto' => null, 'benficiario' => null);
        $form = $this->createFormBuilder($chequeform)
            ->add('cheque')
            ->add('monto')
            ->add('benficiario', 'genemu_jqueryautocomplete_entity', array(
            'class' => 'Cps\chequeBundle\Entity\benficiario',
            'route_name' => 'ajax_benficiario'
        ))->getForm();
        $form->handleRequest($request);


        $lista = $em->getRepository('CpschequeBundle:cheque')->BuscarPorId($comprobanteId);
        /*-----------total ------------*/
        $total = 0;
        $count = 0;
        foreach ($lista as $item) {
            $count++;
            $montot = $item->getMonto();
            $total = $total + $montot;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $chequeData = $data['cheque'];
            $montoData = $data['monto'];
            $bendata = $data['benficiario'];

            if ($chequeData == null) {
                $this->addFlash('error', 'Debe ingresar el Nro de Cheque Correctamente.');
                return $this->redirect($request->getUri());
            }
            $result = $em->getRepository('CpschequeBundle:cheque')->verificar($chequeData);
            $nro = 0;
            foreach ($result as $v) {
                $nro = $nro + 1;
            }
            if ($nro >= 1) {
                $this->addFlash('advertencia', 'El número de cheque ya existe.');
                return $this->redirect($request->getUri());
            }
            if ($montoData == null) {
                $this->addFlash('error', 'Debe ingresar el Monto Correctamente.');
                return $this->redirect($request->getUri());
            }
            $benficiarioid = $em->getRepository('CpschequeBundle:benficiario')->findOneById($bendata);
            if ($benficiarioid == null && $benficiarioid == 0) {
                $this->addFlash('advertencia', 'Debe ingresar el Nombre del  beneficiario correctamente.');
                return $this->redirect($request->getUri());
            }
            else {
                $cheque->setCheque($chequeData);
                $cheque->setMonto($montoData);
                $cheque->setEstado(1);
                $cheque->setActivo(1);
                $cheque->setComprobante($comprobanteC);
                $cheque->setBenficiario($benficiarioid);
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

        return $this->render('cheque/new.html.twig', array(
            'comprobanteC' => $comprobanteC,
            'cheque' => $cheque,
            'lista' => $lista,
            'form' => $form->createView(),
            'formb' => $formb->createView(),
            'total' => $total,
        ));
    }

    /**
     * Finds and displays a cheque entity.
     *
     * @Route("/show/{id}/{proId}", name="cheque_show")
     * @Method("GET")
     */
    public function showAction(cheque $cheque, $id, $proId)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $monto = $cheque->getMonto();
        $nroCheque = $cheque->getCheque();
        $chequef = $em->getRepository('CpschequeBundle:Cheque')->obtenerfechaCheque($nroCheque);
        foreach ($chequef as $item) {
            $valor = $item['fecha'];
        }
        $fecha = $valor->format('Y-m-d');
        $obtenerfecha = new fechaAletraConvertidor();
        $fecha = $obtenerfecha->obtenerFechaEnLetra($fecha);

        $montoLit = explode(".", $monto); //numero literal
        $parteEntera = $montoLit[0];
        if ($monto != $montoLit[0])
            {
            $parteDecimal = $montoLit[1];
        }
        else {
            $parteDecimal = '00';
        }
        $convertidor = new numeroALetraConvertidor();
        $montoLiteral = $convertidor->aLetras($parteEntera);
        $montoLiteral .= $parteDecimal . "/100";

        $this->getDoctrine()->getManager()->flush();
        $cheque->setEstado(2);
        $em->persist($cheque);
        $flush = $em->flush();

        $session = $this->getRequest()->getSession();
        $session->set('chequeId', $id);
        $session->set('proId', $proId);

        return $this->render('cheque/show.html.twig', array(
            'cheque' => $cheque,
            'fecha' => $fecha,
            'montoLiteral' => $montoLiteral,
        ));
    }

    /**
     * Displays a form to edit an existing cheque entity.
     *
     * @Route("/edit/{id}", name="cheque_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, cheque $cheque)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $this->get('security.context')->getToken()->getUser();
        if ($usuario->getServicio()->getId() != 1) {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
        $editForm = $this->createForm('Cps\chequeBundle\Form\chequeType', $cheque);
        $editForm->handleRequest($request);

        $editMonto = $cheque->getMonto();
        $editCheque = $cheque->getCheque();
        $editBen = $cheque->getBenficiario()->getId() .' '.$cheque->getBenficiario()->getNombre();
        $editFor = array('cheque' => $editCheque, 'monto' => $editMonto, 'benficiario' => $editBen);
        $editForm = $this->createFormBuilder($editFor)
            ->add('cheque')
            ->add('monto')
            ->add('benficiario', 'genemu_jqueryautocomplete_entity', array(
            'class' => 'Cps\chequeBundle\Entity\benficiario',
            'route_name' => 'ajax_benficiario'
        ))->getForm();
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();
            $chequeData = $data['cheque'];
            $montoData = $data['monto'];
            if ($editCheque == $chequeData) {
                $benficiarioid = $em->getRepository('CpschequeBundle:benficiario')->findOneById($data['benficiario']);
                if ($benficiarioid == null && $benficiarioid == 0) {

                    $this->addFlash('advertencia', 'El Nombre del Beneficiario es incorrecto, No se ha modificado el registro.');
                    return $this->redirect($request->getUri());
                }
                else {
                    $cheque->setCheque($chequeData);
                    $cheque->setMonto($montoData);
                    $cheque->setBenficiario($benficiarioid);
                    $cheque->setEstado(1);
                    $em->persist($cheque);
                    $em->flush($cheque);
                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash('mensaje', 'El Cheque ha sido Modificado.');
                    return $this->redirectToRoute('cheque_new');
                }
            }
            if ($editCheque != $chequeData) {
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

                    $this->addFlash('advertencia', 'El Nombre del Beneficiario es incorrecto, No se ha modificado el registro.');
                    return $this->redirect($request->getUri());
                }
                else {
                    $cheque->setCheque($chequeData);
                    $cheque->setMonto($montoData);
                    $cheque->setBenficiario($benficiarioid);
                    $cheque->setEstado(1);
                    $em->persist($cheque);
                    $em->flush($cheque);
                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash('mensaje', 'El Cheque ha sido Modificado.');
                    return $this->redirectToRoute('cheque_new');
                }
            }
        }
        return $this->render('cheque/edit.html.twig', array(
            'cheque' => $cheque,
            'edit_form' => $editForm->createView(),
            'editBen' => $editBen,
        ));
    }

    /**
     * Displays a form to edit an existing cheque entity.
     *
     * @Route("/{id}/add", name="cheque_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request, cheque $cheque)
    {
        $em = $this->getDoctrine()->getManager();

        //$cheque->getComprobante()->getId();

        $username = $this->get('security.context')->getToken()->getUser();
        $usuario = $username->getId();

        $editMonto = $cheque->getMonto();
        $editCheque = $cheque->getCheque();
        $editBen = $cheque->getBenficiario()->getId() .' '.$cheque->getBenficiario()->getNombre();
        $editFor = array('cheque' => $editCheque, 'monto' => $editMonto, 'benficiario' => $editBen);
        $editForm = $this->createFormBuilder($editFor)
            ->add('cheque')
            ->add('monto')
            ->add('benficiario', 'genemu_jqueryautocomplete_entity', array(
            'class' => 'Cps\chequeBundle\Entity\benficiario',
            'route_name' => 'ajax_benficiario'
        ))->getForm();
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();
            $chequeData = $data['cheque'];
            $montoData = $data['monto'];
            if ($editCheque == $chequeData) {
                $benficiarioid = $em->getRepository('CpschequeBundle:benficiario')->findOneById($data['benficiario']);
                if ($benficiarioid == null && $benficiarioid == 0) {

                    $this->addFlash('advertencia', 'El Nombre del Beneficiario es incorrecto, No se ha modificado el registro.');
                    return $this->redirect($request->getUri());
                }
                else {
                    $cheque->setCheque($chequeData);
                    $cheque->setMonto($montoData);
                    $cheque->setBenficiario($benficiarioid);
                    $cheque->setEstado(1);
                    $em->persist($cheque);
                    $em->flush($cheque);
                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash('mensaje', 'El Cheque ha sido Modificado.');
                    return $this->redirect($request->getUri());
                }
            }
            if ($editCheque != $chequeData) {
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

                    $this->addFlash('advertencia', 'El Nombre del Beneficiario es incorrecto, No se ha modificado el registro.');
                    return $this->redirect($request->getUri());
                }
                else {
                    $cheque->setCheque($chequeData);
                    $cheque->setMonto($montoData);
                    $cheque->setBenficiario($benficiarioid);
                    $cheque->setEstado(1);
                    $em->persist($cheque);
                    $em->flush($cheque);
                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash('mensaje', 'El Cheque ha sido Modificado.');
                    return $this->redirect($request->getUri());
                }
            }
        }
        return $this->render('cheque/add.html.twig', array(
            'cheque' => $cheque,
            'edit_form' => $editForm->createView(),
            'editBen' => $editBen,
        ));
    }

    /**
     * @Route("/ajax/benficiario/", name="ajax_benficiario")
     */
    public function ajaxAction(Request $request)
    {
        $value = $request->get('term');
        $em = $this->getDoctrine()->getManager();

        $benficiarios = $em->getRepository('CpschequeBundle:benficiario')->findAjaxValue($value);
        // foreach ($benficiarios as $benficiario) {
        //     $id= $benficiario->getId();
        // }
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
