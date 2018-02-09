<?php
namespace Cps\chequeBundle\Controller;

use Cps\chequeBundle\Entity\movimiento;
use Cps\chequeBundle\Entity\comprobante;
use Cps\chequeBundle\Entity\cheque;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Cps\chequeBundle\Libreria\numeroALetraConvertidor;
use Cps\chequeBundle\Libreria\fechaALetraConvertidor;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Movimiento controller.
 *
 * @Route("movimiento")
 */
class movimientoController extends Controller
{
    function getUsuario()
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        return $usuario;
    }
    function getServicio()
    {
        $servicio = $this->get('security.context')->getToken()->getUser()->getServicio();
        return $servicio;
    }
    function getPermiso()
    {
        $permiso = $this->get('security.context')->getToken()->getUser()->getPermiso();
        return $permiso;
    }
    function Pendientes()
    {
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

        return $total;
    }
    function TotalPendientes($valor)
    {
        $id_servicio = $this->getServicio()->getId();
        $em = $this->getDoctrine()->getManager();
        $segTotal = $em->getRepository('CpschequeBundle:movimiento')->segTotal($id_servicio);
        $pendientes = $em->getRepository('CpschequeBundle:movimiento')->segPendiente($id_servicio,$valor);
        $session = $this->getRequest()->getSession();
        $session->set('total', count($segTotal));

        return $pendientes;
    }
    /**
     *
     * @Route("/listado/{estado}", name="movimiento_listado")
     * @Method("GET")
     */
    public function listadoAction(Request $request, $estado)
    {
        $permiso = $this->getPermiso();
        if ($permiso->getM1() == 1) {
            $session = $this->getRequest()->getSession();
            $servicio = $this->getServicio();
            $searchQuery = $request->get('query');
            $em = $this->getDoctrine()->getManager();
            $resComprobante = $em->getRepository('CpschequeBundle:movimiento')->reqCom($servicio->getId(), $searchQuery, $estado);
            if ($resComprobante == null) {
                if ($searchQuery != null) {
                    $this->addFlash('mensaje', 'No se han encontrado resultados. con el número de comprobante' . " " . $searchQuery);
                }
            }
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($resComprobante, $request->query->getInt('page', 1), 11);
            $session->set('estado', $estado);

            return $this->render('movimiento/index.html.twig', array(
                'pagination' => $pagination,
                'estado' => $estado
            ));
        } else {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     *
     * @Route("/pendiente", name="movimiento_pendiente")
     * @Method("GET")
     */
    public function movimientoPendienteAction(Request $request)
    {
        $valor = $request->get('query');
        $servicio = $this->getServicio();
        $pendientes = $this->TotalPendientes($valor);
        if ($pendientes == 0 or  $pendientes == null) {
            if ($servicio->getId() == 5) {
                return $this->redirectToRoute('movimiento_historial');
            } else {
                return $this->redirectToRoute('movimiento_listado', array(
                    'estado' => 1
                ));
            }
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($pendientes, $request->query->getInt('page', 1),11);
        return $this->render('movimiento/pendiente.html.twig', array(
            'pendientes' => $pagination
        ));
    }

    /**
     *
     * @Route("/historial", name="movimiento_historial")
     * @Method({"GET"})
     */
    public function archivadoAction(Request $request)
    {
        $valor="";
        $permiso = $this->getPermiso()->getM1();
        if($permiso){
            $this->TotalPendientes($valor);
            $servicio = $this->getServicio();
            $searchQuery = $request->get('query');
            $em = $this->getDoctrine()->getManager();
            $archivados = $em->getRepository('CpschequeBundle:movimiento')->ComprobanteArchivados($servicio->getId(), $searchQuery);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($archivados, $request->query->getInt('page', 1), 11);

            return $this->render('movimiento/archivado.html.twig', array(
                'pagination' => $pagination
            ));
        } else {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Finds and displays a movimiento entity.
     *
     * @Route("/movimiento/{id}", name="movimiento_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(movimiento $movimiento, request $request)
    {
        $permiso = $this->getPermiso()->getM1();
        if($permiso){
            $usuario = $this->getUsuario();
            $id_servicio = $this->getServicio()->getId();
            $session = $this->getRequest()->getSession();

            $formulario = array('servicio' => null, 'observacion' => null);
            $form = $this->createFormBuilder($formulario)
                ->add('servicio', 'entity', array(
                'label' => 'Servicio :',
                'class' => 'CpschequeBundle:Servicio',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($id_servicio) {
                    return $er->createQueryBuilder('s')
                        ->where('s.id < :id')
                        ->orderBy('s.id', 'ASC')
                        ->setParameter('id', $id_servicio);
                },
            ))
                ->add('observacion', 'text', array('label' => 'Motivo :'))
                ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                /*Modificando el movimiento*/
                $movimiento->setEstado(2);
                $movimiento->setUserEnviado($usuario->getId());
                $movimiento->setEnviadoEL(new \DateTime("now"));
                $em->persist($movimiento);
                $em->flush($movimiento);
                /*Creando el nuevo movimiento al devolver a otro servicio*/
                $movimiento1 = new Movimiento();
                $data = $form->getData();
                $movimiento1->setObservacion($data['observacion']);
                $movimiento1->setEstado(0);
                $movimiento1->setEsActivo(1);
                $movimiento1->setUserRecibido(0);
                $movimiento1->setUserEnviado(0);
                $movimiento1->setCreadoEl(new \DateTime("now"));
                $movimiento1->setServicio($data['servicio']);
                $movimiento1->setComprobante($movimiento->getComprobante());
                $em->persist($movimiento1);
                $em->flush($movimiento1);

                $this->addFlash('mensaje', 'Se ha devuelto el comprobante a ' . $data['servicio'] . '.');
                return $this->redirectToRoute('homepage');
            }
            $estado = $session->get('estado');
            return $this->render('movimiento/show.html.twig', array(
                'movimiento' => $movimiento,
                'form' => $form->createView(),
                'estado' => $estado
            ));
        } else {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Creates a new movimiento entity.
     *
     * @Route("/new", name="movimiento_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $permiso = $this->getPermiso();
        if ($permiso->getM1() == 1) {
            $usuario = $this->getUsuario();
            $servicio = $this->getServicio();

            $movimiento = new Movimiento();
            $chequeform = array('observacion' => null, 'comprobante' => null);
            $form = $this->createFormBuilder($chequeform)
                ->add('comprobante', 'genemu_jqueryautocomplete_entity', array(
                'class' => 'Cps\chequeBundle\Entity\movimiento',
                'route_name' => 'ajax_comprobante'
            ))
                ->add('observacion')
                ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                if (true) {
                    $em = $this->getDoctrine()->getManager();
                    $comprobante_id = $em->getRepository('CpschequeBundle:comprobante')->findOneById($data['comprobante']);
                    $movimiento->setServicio($servicio);
                    $movimiento->setUserRecibido($usuario->getId());
                    $movimiento->setRecibidoEL(new \Datetime("now"));
                    $movimiento->setEstado(1);
                    $movimiento->setUserEnviado(0);
                    $movimiento->setEsActivo(1);
                    $movimiento->setComprobante($comprobante_id);
                    $em->persist($movimiento);
                    $em->flush($movimiento);

                } else {
                    //TODO PENDIENTE
                }
                return $this->redirectToRoute('movimiento_listado', array('estado' => '1'));
            }
            return $this->render('movimiento/new.html.twig', array(
                'movimiento' => $movimiento,
                'form' => $form->createView()
            ));
        } else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     *
     * @Route("/recibir/{id}", name="recibir")
     * @Method("GET")
     */
    public function recibirAction(movimiento $movimiento, Request $request)
    {
        $permiso = $this->getPermiso()->getM1();
        if ($permiso) {
            $em = $this->getDoctrine()->getManager();
            $id_servicio = $this->getServicio()->getId();
            $id_usuario = $this->getUsuario()->getId();
            if ($id_servicio == 5) {
                $movimiento->setEstado(3);
            } else {
                $movimiento->setEstado(1);
            }
            $movimiento->setUserRecibido($id_usuario);
            $movimiento->setRecibidoEL(new \Datetime("now"));
            $em->persist($movimiento);
            $flush = $em->flush();

            return $this->redirectToRoute('movimiento_pendiente');
        } else {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     *
     * @Route("/enviar/{id}", name="enviar")
     * @Method("GET")
     */
    public function enviarAction(movimiento $movimiento, Request $request, $id)
    {
        $as_Permiso = $this->getPermiso()->getM1();
        if ($as_Permiso) {
            $em = $this->getDoctrine()->getManager();
            $id_servicio = $this->getServicio()->getId();
            $movimiento_id = $em->getRepository('CpschequeBundle:movimiento')->reqComprobante($id, $id_servicio);
            foreach ($movimiento_id as $item) {
                $idComprobante = $item->getComprobante();
            }
            $as_cheque = $em->getRepository('CpschequeBundle:movimiento')->verificarCheques($idComprobante);
            $as_prioridad = $this->getServicio()->getPrioridad();
            if ($as_cheque != null) {

                $id_usuario = $this->getUsuario()->getId();
                $movimiento->setEstado(2);
                $movimiento->setUserEnviado($id_usuario);
                $movimiento->setEnviadoEL(new \Datetime("now"));
                $em->flush($movimiento);

                if ($as_prioridad != 0) {
                    $comprobante_id = $em->getRepository('CpschequeBundle:comprobante')->findOneById($idComprobante);
                    $servicio_id = $em->getRepository('CpschequeBundle:servicio')->findOneByPrioridad($as_prioridad + 1);
                    $movimiento1 = new Movimiento();
                    $movimiento1->setServicio($servicio_id);
                    $movimiento1->setUserRecibido(0);
                    $movimiento1->setUserEnviado(0);
                    $movimiento1->setEstado(0);
                    $movimiento1->setEsActivo(1);
                    $movimiento1->setComprobante($comprobante_id);
                    $em->persist($movimiento1);
                    $em->flush($movimiento1);
                    $this->addFlash('mensaje', 'Se ha enviado el comprobante correctamente.');
                }
            } else {
                $this->addFlash('advertencia', 'No se ha podido enviar el comprobante.');
            }
            return $this->redirectToRoute('movimiento_listado', array(
                'estado' => 1
            ));
        } else {
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/ajax/comprobante/", name="ajax_comprobante")
     */
    public function ajaxAction(Request $request)
    {
        $value = $request->get('term');
        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('CpschequeBundle:comprobante')->findAjaxValue($value);

        $json = array();
        foreach ($comprobantes as $comprobante) {
            $json[] = array(
                'label' => " Comprobante : " . $comprobante->getBte() . ", Doc. Respaldo : " . $comprobante->getDocres(),
                'value' => $comprobante->getId() . " Comprobante : " . $comprobante->getBte() . ", Doc. Respaldo : " . $comprobante->getDocres()
            );
        }
        $response = new Response(json_encode($json));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Finds and displays a movimiento entity.
     *
     * @Route("/seguimiento/{idCheque}", name="seguimiento_cheque")
     * @Method("GET")
     */
    public function seguimientoAction($idCheque)
    {
        if(true){
            $em = $this->getDoctrine()->getManager();
            $cheque = $em->getRepository('CpschequeBundle:cheque')->find($idCheque);

            $fecha = $cheque->getComprobante()->getFecha()->format('Y-m-d');
            $obtenerfecha = new fechaAletraConvertidor();
            $fecha = $obtenerfecha->obtenerFechaEnLetra($fecha);

            $montoLit = explode(".", $cheque->getMonto()); //numero literal
            $parteEntera = $montoLit[0];
            if ($cheque->getMonto() != $montoLit[0]) {
                $parteDecimal = $montoLit[1];
            } else {
                $parteDecimal = '00';
            }
            $convertidor = new numeroALetraConvertidor();
            $montoLiteral = $convertidor->aLetras($parteEntera);
            $montoLiteral .= $parteDecimal . "/100";

            return $this->render('movimiento/seguimiento.html.twig', array(
                'cheque' => $cheque,
                'fecha' => $fecha,
                'montoLiteral' => $montoLiteral,
            ));
        }else{
            $this->addFlash('mensaje', 'No Tienes permiso para ingresar a la dirección seleccionada.');
            return $this->redirectToRoute('homepage');
        }
    }
    /**
     * Finds and displays a movimiento entity.
     *
     * @Route("/reporte/servicio", name="seguimiento_reporte")
     * @Method({"GET", "POST"})
     */
    public function reporteAction(Request $request)
    {
        $result = array();
        $dateForm = array('fecha_ini' => null, 'fecha_fin' => null);
        $form = $this->createFormBuilder($dateForm)
            ->add('fecha_ini', 'genemu_jquerydate', array(
                'widget' => 'single_text',
                'html5' => 'true',
            ))
            ->add('fecha_fin', 'genemu_jquerydate', array(
                'widget' => 'single_text',
                'html5' => 'true',
            ))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $fecha_ini = $data['fecha_ini'];
            $fecha_fin = $data['fecha_fin'];
            if ($fecha_ini != null and $fecha_fin != null) {
                $servicio = $this->getServicio();
                $em = $this->getDoctrine()->getManager();

//                dump($fecha_ini,$fecha_fin,$servicio->getNombre());die;
                $result = $em->getRepository('CpschequeBundle:movimiento')->reporteMXServicio(
                    $fecha_ini,
                    $fecha_fin,
                    $servicio->getId()
                );
                  //  dump($result);die;
                if ($result != null){
                    $this->addFlash('success', 'Se ha generado correctamente el reporte.');
                    $session = $this->getRequest()->getSession();
                    $session->set('resultExcel', $result);
                    $session->set('fecha_ini', $fecha_ini);
                }else {
                    $this->addFlash('info', 'Sin resultados.');
                }

            } else {
                $this->addFlash('warning', 'Elige un rango de fecha.');
            }
        }
        return $this->render('movimiento/reporte.html.twig', array(
            'form' => $form->createView(),
            'result' => $result
        ));
    }

    /**
     *
     * @Route("/reporte/excel", name="export_excel_servicio")
     * @Method({"GET", "POST"})
     */
    public function exportExcelAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $result= $session->get('resultExcel');
        $fini= $session->get('fecha_ini');
        if($result !=  null){
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            $phpExcelObject->getProperties()
            ->setCreator("Vabadus")
            ->setLastModifiedBy("Vabadus")
            ->setTitle("Reporte de seguimiento de cheques.")
            ->setSubject("Excel Cheque")
            ->setDescription("Listado.")
            ->setKeywords("vabadus exportar excel ejemplo");

            $mesliteral = new fechaAletraConvertidor();
            $getDate = $fini->format('d-m-Y');
            $fecha = $mesliteral->obtenermes($getDate);

            // establecemos como hoja activa la primera, y le asignamos un t�tulo
            $phpExcelObject->setActiveSheetIndex(0);
            $phpExcelObject->getActiveSheet()->setTitle('Excel.');

            // escribimos en distintas celdas del documento el t�tulo de los campos que vamos a exportar
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B2', 'FECHA')
                ->setCellValue('C2', 'COMPROBANTE')
                ->setCellValue('D2', 'CHEQUE')
                ->setCellValue('E2', 'NOMBRE / EMPRESA')
                ->setCellValue('F2', 'MONTO')
                ->setCellValue('G2', 'RECIBIDO EL')
                ->setCellValue('H2', 'ENVIADO EL');

            // fijamos un ancho a las distintas columnas
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(13);
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(40);
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(20);

            $row = 3;

            foreach ($result as $item) {
                $fechaxl = $item['fecha'];
                if ($fechaxl != null ) {
                  $fechaxl = $fechaxl->format('d/m/Y');
                } else{
                  $fechaxl = "Nulo";
                }

                $fechaxll = $item['recibidoEL'];
                if ($fechaxll != null ) {
                  $fechaxll = $fechaxll->format('d/m/Y');
                }else{
                  $fechaxll = "Nulo";
                }

                $fechaxlll = $item['enviadoEL'];
                if ($fechaxlll != null ) {
                    $fechaxlll = $fechaxlll->format('d/m/Y');
                }else{
                  $fechaxlll = "No se ha enviado.";
                }
                $monto = number_format($item['monto'], 2, ',', '.');

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B' . $row, $fechaxl)
                    ->setCellValue('C' . $row, $item['bte'])
                    ->setCellValue('D' . $row, $item['cheque'])
                    ->setCellValue('E' . $row, strtoupper ($item['nombre']))
                    ->setCellValue('F' . $row, $monto)
                    ->setCellValue('G' . $row, $fechaxll)
                    ->setCellValue('H' . $row, $fechaxlll);
                $row++;
            }
            // se crea el writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
            // se crea el response
            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // y por �ltimo se a�aden las cabeceras
            $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'Reporte_Seguimiento_'.$fecha.'.xls'
            );
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');
            $response->headers->set('Content-Disposition', $dispositionHeader);
            return $response;
        }
    }
}