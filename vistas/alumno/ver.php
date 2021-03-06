<?php
//---------------------------------------------------------------------------
//Vista de CONSULTA de clientes...
//---------------------------------------------------------------------------
// Datos que recibe:
//    $modelo --> Instancia con un modelo "Cliente" a visualizar o "null" si
//                hubo error de carga.
//    $error  --> Mensaje de error o cadena vacia si no hubo.
//    $pagina --> numero de pagina que se esta obteniendo.
//---------------------------------------------------------------------------
/*-----
depurar( array( 
  'id_controlador' => aplicacion::$id_controlador,
  'id_accion' => aplicacion::$id_accion,
  'modelo' => $modelo,
  'error' => $error,
));
//-----*/
?>
<div id="main">
<div class="inner">
<h1>Ver Alumno</h1>
<div class="hoja">
<table>
<?php //Generar el cuerpo de la tabla con la ficha de cliente.
vista::generarParcial( 'alumno_ficha', array( 'modelo'=>$modelo, 'error'=>$error));
?>
<tfoot>
<tr>
  <td colspan="2" class="cen">
  <div class="acciones">
<?php //Generar el pie de la tabla con las acciones.
//if (tiene_permiso( 'alumno.editar')) {
  vista::generarPieza( 'boton_accion', array( 'texto'=>'Editar', 
    'activo'=>true, 'url'=>array('a'=>'alumno.editar', 'id'=>$modelo->id, 'p'=>$pagina)));
  echo '<div></div>';
  vista::generarPieza( 'boton_accion', array( 'texto'=>'Eliminar', 
    'activo'=>true, 'url'=>array('a'=>'alumno.borrar', 'id'=>$modelo->id, 'p'=>$pagina)));
//}//if "permiso"
  echo '<div></div>';

//Generar el boton para VOLVER.
vista::generarPieza( 'boton_accion', array( 'texto'=>'Volver',
  'activo'=>true, 'url'=>array('a'=>'alumno', 'p'=>$pagina)));
?>
  </div>
  </td>
</tr>
</tfoot>
</table>
</div>
</div>
</div>