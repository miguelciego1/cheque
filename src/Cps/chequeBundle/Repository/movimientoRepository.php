<?php

namespace Cps\chequeBundle\Repository;

/**
 * movimientoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class movimientoRepository extends \Doctrine\ORM\EntityRepository
{
    public function segTotal($id_servicio){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT m.id
                                     FROM  CpschequeBundle:movimiento m
                                     WHERE m.servicio = :id_servicio
                                        and m.esActivo = 1
                                        and m.estado = 0")
            ->setParameter('id_servicio',$id_servicio);
        $result = $query->getResult();
        return $result;
    }
    public function segPendiente($id_servicio, $valor){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT m
                                     FROM  CpschequeBundle:movimiento m
                                     join m.comprobante c
                                     WHERE c.bte LIKE :valor
                                        and m.servicio = :id_servicio
                                        and m.esActivo = 1
                                        and m.estado = 0")
            ->setParameter('id_servicio',$id_servicio)
            ->setParameter('valor', '%'.$valor.'%');
        $result = $query->getResult();
        return $result;
    }
    public function reqCom($id_servicio, $searchQuery, $estado){
        $em=$this->getEntityManager();
        $query = $em->createQuery("SELECT m.id as movimientoId, m.creadoEl as recibido, m.enviadoEL as enviado, c.id, c.bte, c.docres ,c.glosa, s.nombre as servicio
                                     FROM CpschequeBundle:Comprobante c
                                     JOIN c.movimiento m
                                     JOIN m.servicio s
                                     WHERE c.activo = 1 and m.esActivo = 1 and c.bte LIKE '%$searchQuery%' and m.servicio = :id_servicio and m.estado = $estado and m.esActivo = 1 ORDER BY m.id DESC")
                                     ->setParameter('id_servicio',$id_servicio);
        $result = $query->getResult();
        return $result;
    }
    public function verificarCheques($idComprobante){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT c
                                     FROM CpschequeBundle:cheque c
                                     WHERE c.activo = 1 and c.comprobante = :comprobante_id and c.estado > 1")
            ->setParameter('comprobante_id',$idComprobante);
        $result = $query->getResult();
        return $result;
    }
    public function reqComprobante($id, $id_servicio){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT m
                                     FROM CpschequeBundle:movimiento m
                                     WHERE m.esActivo = 1 and m.id = :id and m.servicio = :id_servicio")
            ->setParameter('id',$id)
            ->setParameter('id_servicio',$id_servicio);
        $result = $query->getResult();
        return $result;
    }
    public function ComprobanteArchivados($id_servicio, $searchQuery){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT m
                                     FROM CpschequeBundle:movimiento m
                                     JOIN m.comprobante c
                                     WHERE m.esActivo = 1 and c.activo = 1 and c.bte LIKE '%$searchQuery%' and m.servicio = :id_servicio and m.estado = 3 and m.esActivo = 1 Order by m.recibidoEL DESC")
            ->setParameter('id_servicio',$id_servicio);
        $result = $query->getResult();
        return $result;
    }
    public function busCheques($nombre){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT q.id as chequeId, c.id as comprobanteId, c.fecha, q.estado, c.glosa, b.nombre, q.cheque, c.docres, c.bte, q.monto
                                     FROM CpschequeBundle:movimiento m
                                     JOIN m.comprobante c
                                     JOIN c.cheque q
                                     JOIN q.benficiario b
                                     WHERE c.activo = 1 and b.nombre LIKE '%$nombre%' GROUP BY c.bte");
        $result = $query->getResult();
        return $result;
    }
    public function reporteMXServicio($fecha_ini,$fecha_fin,$servicio_id){
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT c.fecha, c.bte, q.cheque, b.nombre , q.monto, m.recibidoEL, m.enviadoEL, q.estado
                                     FROM CpschequeBundle:movimiento m
                                     JOIN m.comprobante c
                                     JOIN c.cheque q
                                     JOIN q.benficiario b
                                     WHERE m.recibidoEL BETWEEN :ini and :fin and m.servicio = :id_serv and  c.activo = 1 and m.estado >= 1 ORDER BY c.fecha ASC")
                                     ->setParameter(':ini', $fecha_ini)
                                     ->setParameter(':fin', $fecha_fin)
                                     ->setParameter(':id_serv', $servicio_id);
        $result = $query->getResult();
        return $result;
    }


//    public function updateMovimiento($id_movimiento, $id_servicio, $fecha){
//        $em=$this->getEntityManager();
//        $query = $em->createQuery("UPDATE CpschequeBundle:movimiento m SET m.estado = 2, $fecha = CURRENT_DATE() WHERE m.id IN (:ids) and m.servicio = $id_servicio and m.estado = 1")
//                    ->setParameter(':ids', $id_movimiento, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
//        $result = $query->getResult();
//        return $result;
//    }

}
