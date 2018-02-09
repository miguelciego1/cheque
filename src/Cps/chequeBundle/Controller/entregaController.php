<?php

namespace Cps\chequeBundle\Controller;
use Cps\chequeBundle\Entity\cheque;
use Cps\chequeBundle\Entity\entrega;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class entregaController extends Controller
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

    function TotalPendientes()
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

        return $pendientes;
    }

    /**
     *
     * @Route("/entrega/{id}", name="cheque_entrega")
     * @Method("GET")
     */
    public function entregaAction(cheque $cheque ,Request $request)
    {
        if ($cheque->getEstado() == 2){
            $em = $this->getDoctrine()->getManager();
            $usuario = $this->getUsuario();
            $cheque->setEstado(5);
            $em->persist($cheque);

            $entrega =  new entrega();
            $entrega->setFecha(new \Datetime("now"));
            $entrega->setEsActivo(1);
            $entrega->setCheque($cheque);
            $entrega->setUsuario($usuario);
            $em->persist($entrega);

            $em->flush();

            $this->addFlash('mensaje', 'Se ha entregado el cheque con el número ' ." ". $cheque->getCheque() .','. ' Al Beneficiario'.' '.$cheque->getBenficiario()->getNombre());
            return $this->redirectToRoute('entregados');
        } else{
            return $this->redirectToRoute('entregados');
        }
    }
    /**
     *
     * @Route("/entregados", name="entregados")
     * @Method("GET")
     */
    public function recibirAction(request $request)
    {
        $pendientes = $this->TotalPendientes();
        $em = $this->getDoctrine()->getManager();
        $entregados = $em->getRepository('CpschequeBundle:entrega')->reqEntregados();
        // dump($entregados);die;
        $session = $this->getRequest()->getSession();
        $session->set('resultExcelEntregado', $entregados);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate( $entregados, $request->query->getInt('page', 1), 11);
        return $this->render('entrega/entregados.html.twig', array(
            'pagination' => $pagination
        ));
    }
    /**
     *
     * @Route("/reporte/excel/entregados", name="export_excel_entregado")
     * @Method({"GET", "POST"})
     */
    public function exportExcelEntregadoAction(Request $request)
    {
      $session = $this->getRequest()->getSession();
      $result = $session->get('resultExcelEntregado');
      // dump($result);die;

      if($result !=  null){
          $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

          $phpExcelObject->getProperties()
          ->setCreator("dev.fernandocto")
          ->setLastModifiedBy("dev.fernandocto")
          ->setTitle("Reporte de cheques entregados.")
          ->setSubject("Cheques Entregados")
          ->setDescription("Listado.")
          ->setKeywords("Exportar excel ejemplo");
          //
          // $mesliteral = new fechaAletraConvertidor();
          // $getDate = $fini->format('d-m-Y');
          // $fecha = $mesliteral->obtenermes($getDate);

          // establecemos como hoja activa la primera, y le asignamos un t�tulo
          $phpExcelObject->setActiveSheetIndex(0);
          $phpExcelObject->getActiveSheet()->setTitle('Excel.');

          // escribimos en distintas celdas del documento el t�tulo de los campos que vamos a exportar
          $phpExcelObject->setActiveSheetIndex(0)
              ->setCellValue('B2', 'COMPROBANTE')
              ->setCellValue('C2', 'NOMBRE / EMPRESA')
              ->setCellValue('D2', 'CHEQUE')
              ->setCellValue('E2', 'FECHA DEL CHEQUE')
              ->setCellValue('F2', 'MONTO')
              ->setCellValue('G2', 'ENTREGADO');

          // fijamos un ancho a las distintas columnas
          $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
          $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(50);
          $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);
          $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
          $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
          $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
          $row = 3;

          foreach ($result as $item) {
              $fechaxl = $item->getFecha();
              $xlsxfecha = $fechaxl->format('d/m/Y');

              $fecha_impresion = $item->getCheque()->getComprobante()->getFecha();
              $xlsxfecha_impresion = $fecha_impresion->format('d/m/Y');

              $nombre_empresa = strtoupper ($item->getCheque()->getBenficiario()->getNombre());
              $monto = number_format($item->getCheque()->getMonto(), 2, ',', '.');

              $phpExcelObject->setActiveSheetIndex(0)
                  ->setCellValue('B' . $row, $item->getCheque()->getComprobante()->getBte())
                  ->setCellValue('C' . $row, $nombre_empresa)
                  ->setCellValue('D' . $row, $item->getCheque()->getCheque())
                  ->setCellValue('E' . $row, $xlsxfecha_impresion)
                  ->setCellValue('F' . $row, $monto)
                  ->setCellValue('G' . $row, $xlsxfecha);
              $row++;
          }
          // se crea el writer
          $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
          // se crea el response
          $response = $this->get('phpexcel')->createStreamedResponse($writer);
          // y por ultimo se a�aden las cabeceras
          $dispositionHeader = $response->headers->makeDisposition(
              ResponseHeaderBag::DISPOSITION_ATTACHMENT,
              // 'Reporte_Seguimiento_'.$fecha.'.xls'
              'reporte.xls'
          );
          $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
          $response->headers->set('Pragma', 'public');
          $response->headers->set('Cache-Control', 'maxage=1');
          $response->headers->set('Content-Disposition', $dispositionHeader);
          return $response;
      }
    }
}
