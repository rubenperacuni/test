<?php
modelo::usar( 'alumno');
modelo::usar( 'pregunta');

class controlador_test extends controlador
{
  public $accion_defecto= 'test';

  public $preguntas = null;		//Array de objetos pregunta.
  public $alumno = null;			//Instancia del modelo alumno.
  
  //-------------------------------------------------------------------------
  public function accion_test()
  {
    // Alumno que realiza el test
    $this->alumno = new alumno();
    // Array de preguntas del test.
    $this->preguntas = array();
    $pregunta = new pregunta;
    
    // Si hay post se hace la corrección del test, de lo contrario se hace
    if (!empty($_POST)) {
      
      foreach ($_SESSION['respuestas'] as $res => $r ) {

        if (isset($_POST[$r['id']])) {
          $respuestas=array('respuesta'=>$_POST[$r['id']] );
          // echo 'MERGEO';
          $_SESSION['respuestas'][$res]=array_merge($_SESSION['respuestas'][$res],$respuestas );
        }

      }
      // Se genera la página con las preguntas del test.
      vista::generarPagina( 'resultado', array( 'preguntas'=>$_SESSION['respuestas']));

    }else{
      // Si el alumno tiene el nivel adecuado para hacer el test del nivel indicado por GET
      if (isset($_GET['nivel']) && $this->alumno->nivel=$_GET['nivel']) {
      
        $_SESSION['respuestas']=[];
        // Se escogen todas las preguntas del nivel seleccionado
        

        $sql=pregunta::sqlBuscar(array('nivel'=>$_GET['nivel']));
        // Se obtienen los 10 primeros resultados
        $preguntas=basedatos::obtenerTodos( $sql,-1,10);

        // Se guardan las preguntas en una variable de sesión.
        $_SESSION['respuestas']=[];
        foreach ($preguntas as $pregunta)
          array_push($_SESSION['respuestas'], $pregunta);
        // Se genera la página con las preguntas del test.
        vista::generarPagina( 'test', array( 'preguntas'=>$_SESSION['respuestas']));
      }else{
            vista::redirigir( array('alumno','alumno'));
      }
    }
  }//accion_test

  //-------------------------------------------------------------------------
  //Cargar el alumno logueado.
  public function cargarAlumno()
  {
    //Si el cliente no esta cargado, o si lo esta, hay referencia en el pedido
    //y las referencias no coinciden entre si, se intenta cargar.
    if (($this->alumno === null) || 
          (($this->alumno !== null) && !empty($this->refCli) 
              && ($this->alumno->id != $this->refCli))) {
      //Crear la instancia nueva y cargarla, y si falla, dejarla nula.
      $this->alumno= new alumno;
      if (!$this->alumno->cargar( $this->refCli)) $this->alumno= null;
    }//if
    return ($this->alumno !== null);
  }//cargarCliente
  
  //-------------------------------------------------------------------------

  
  //-------------------------------------------------------------------------
  //Accion para CREAR un pregunta
  public function accion_crear()
  {
    $bien= false;
    $error= '';
    $modelo= new pregunta;
    //----------
    $pagina= (int)(isset($_GET['p']) ? $_GET['p'] : 0);//coger la pagina para poder volver
    //----------
    //Si hay datos del formulario pregunta, se intenta crear nueva...
    if (isset($_POST['pregunta'])) {
      //Copiar los datos del formulario...
      $modelo->llenar( $_POST['pregunta']);
      //Intentar guardar validando antes el modelo...
      $bien= $modelo->guardar();
      if ($bien) $error= 'El pregunta se ha guardado correctamente.';
      else $error= 'No se ha podido guardar la pregunta nueva.';
    }//if
    //----------
    //Dar una respuesta segun el resultado del proceso.
    if ($bien) {
      //vista::redirigir( array('preguntas.editar'), array('id'=>$modelo->referencia, 'p'=>$pagina));
      vista::generarPagina( 'editar', array( 
        'modelo'=>$modelo,
        'error'=>$error,
        'pagina'=>$pagina,
        'guardado'=>true,
      ));
    } else {
      vista::generarPagina( 'crear', array( 
        'modelo'=>$modelo,
        'error'=>$error,
        'pagina'=>$pagina,        
      ));
    }//if
    //-----*/
  }//accion_crear
  
  //-------------------------------------------------------------------------
  //Accion para EDITAR un pregunta
  public function accion_editar()
  {
    $bien= false;
    $error= '';
    $modelo= null;
    //----------
    $pagina= (int)(isset($_GET['p']) ? $_GET['p'] : 0);//coger la pagina para poder volver
    //----------
    //Coger el dato clave para cargar el modelo a editar...
    $id= (isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null));
    if ($id === null) {
      $error= 'No se ha indicado el pregunta a editar.';
    } else {
      $modelo= new pregunta;
      if (!$modelo->cargar( $id)) {
        $error= 'No se puede cargar el pregunta ('.$id.') para editar.';
        $modelo= null;
      }//if
    }//if
    //----------
    //Si hay modelo cargado, y datos del formulario, se intenta copiar/guardar.
    if (($modelo !== null) && isset($_POST['pregunta'])) {
      //Copiar los datos del formulario...
      $modelo->llenar( $_POST['pregunta']);
      //Intentar guardar validando antes el modelo...
      $bien= $modelo->guardar();
      if ($bien) $error= 'El pregunta se ha guardado correctamente.';
      else $error= 'No se ha podido guardar la pregunta ('.$id.').';
    }//if
    //----------
    //Dar una respuesta segun el resultado del proceso.
    //--if ($bien) {
    //--  vista::redirigir( array('preguntas'), array('p'=>$pagina));
    //--} else {
      vista::generarPagina( 'editar', array( 
        'modelo'=>$modelo,
        'error'=>$error,
        'pagina'=>$pagina,
      ));
    //--}//if
    //-----*/
  }//accion_editar
  
  //-------------------------------------------------------------------------
  //Accion para CONSULTAR un pregunta
  public function accion_ver()
  {
    $bien= false;
    $error= '';
    $modelo= null;
    //----------
    $pagina= (int)(isset($_GET['p']) ? $_GET['p'] : 0);//coger la pagina para poder volver
    //----------
    //Coger el dato clave para cargar el modelo a editar...
    $id= (isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null));
    if ($id === null) {
      $error= 'No se ha indicado el pregunta a consultar.';
    } else {
      $modelo= new pregunta;
      if (!$modelo->cargar( $id)) {
        $error= 'No se puede cargar el pregunta ('.$id.') para consultar.';
        $modelo= null;
      }//if
    }//if
    //----------
    //Dar una respuesta segun el resultado del proceso.
    vista::generarPagina( 'ver', array(
      'modelo'=>$modelo,
      'error'=>$error,
      'pagina'=>$pagina,
    ));
  }//accion_ver
  
  //-------------------------------------------------------------------------
  //Accion para ELIMINAR un pregunta
  public function accion_borrar()
  {
    $bien= false;
    $error= '';
    $modelo= null;
    //----------
    $pagina= (int)(isset($_GET['p']) ? $_GET['p'] : 0);//coger la pagina para poder volver
    //----------
    //Coger el dato clave para cargar el modelo a editar...
    $id= (isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null));
    if ($id === null) {
      $error= 'No se ha indicado el pregunta a editar.';
    } else {
      $modelo= new pregunta;
      if (!$modelo->cargar( $id)) {
        $error= 'No se puede cargar el pregunta ('.$id.') para editar.';
        $modelo= null;
      }//if
    }//if
    //----------
    $confirmado= (boolean)(isset($_GET['ok']) ? $_GET['ok'] : (isset($_POST['ok']) ? $_POST['ok'] : 0));
    //----------
    //Si hay modelo cargado, y datos del formulario, se intenta eliminar.
    if (($modelo !== null) && $confirmado) {
      //Intentar eliminar el modelo...
      $bien= $modelo->eliminar();
      if ($bien) $error= 'El pregunta se ha eliminado correctamente.';
      else $error= 'No se ha podido eliminar el pregunta ('.$id.').';
    }//if
    //----------
    //Dar una respuesta segun el resultado del proceso.
    if ($bien) {
      vista::redirigir( array('pregunta'), array('p'=>$pagina));
    } else {
      vista::generarPagina( 'borrar', array(
        'modelo'=>$modelo,
        'error'=>$error,
        'pagina'=>$pagina,
      ));
    }//if
  }//accion_borrar
  
  //-------------------------------------------------------------------------
  //Accion para CREAR modelos de pregunta de ejemplo.
  //Eliminar o comentar cuando no se use.
  /*-----*/
  public function accion_creardemo()
  {
    $bien= false;
    $modelo= new pregunta;
    //----------
    //Simular la creacion de varios preguntas...
    //INSERT INTO `preguntas`
    // (`referencia`, `cifnif`, `nombre`, `apellidos`, `domFiscal`, `domEnvio`, `notas`, `email`, `password`)
    // VALUES
    // ('ZA000003', 'asdoiu', 'oiuoiu', 'oiuoiuoiu', 'oiuoiuoiu', '', NULL, 'email', 'clave')
    for ($i= 1; ($i <= 25); $i++) {
      $modelo->nivel= sprintf( '1', $i);
      $modelo->nombre= sprintf( 'nombre%d', $i);
      $modelo->apellidos= sprintf( 'apellidos pregunta%d', $i);
      $modelo->email= sprintf( 'pregunta%d@correo.es', $i);
      $modelo->password= sprintf( 'pregunta%d', $i);
      $modelo->guardar();
      //crear nueva instancia para que se inserte el siguiente.
      $modelo= new pregunta;
    }//for
    //--echo 'voy a redirigir la pagina...'; flush();//probar a generar contenido HTML antes de redirigir.
    vista::redirigir( array('pregunta','admin'));
  }//accion_creardemo
  
  //-------------------------------------------------------------------------
  //Accion para EDITAR un modelo de pregunta de ejemplo.
  public function accion_editardemo()
  {
    $bien= false;
    //----------
    //Simular la modificacion de los datos de pregunta... En concreto la clave primaria...
    $modelo= new pregunta;
    $id1= 'ZA000001';
    $id2= 'VA000001';
    $bien= $modelo->cargar( $id1);
    if (!$bien) {
      $id3= $id1;
      $id1= $id2;
      $id2= $id3;
      $bien= $modelo->cargar( $id1);
    }//if
    if ($bien) {
      depurar( array( 
        'modelo.cargado'=> print_r( $modelo,true)
      ));
      $modelo->referencia= $id2;
      if ($modelo->guardar()) {
        $info= 'Modelo actualizado correctamente.';
      } else {
        $info= 'Modelo no actualizado.';
      }//if
      depurar( array( 
        'info'=>$info,
        'modelo.guardado'=> print_r( $modelo,true)
      ));
    } else {
      echo 'No se ha podido cargar ninguna de las pruebas.';
    }//if
  }//accion_editardemo
  
  //-------------------------------------------------------------------------
  //Accion para ELIMINAR un modelo de pregunta de ejemplo.
  public function accion_borrardemo()
  {
    $bien= false;
    //----------
    //Simular la eliminacion de los datos de pregunta... En concreto la clave primaria...
    $modelo= new pregunta;
    $borrado= sesion::get( 'pregunta.borrado', null);
    if ($borrado !== null) {
      $modelo= $borrado;
      $bien= $modelo->guardar();
      if ($bien) {
        depurar( array( 
          'modelo.sesion.guardado' => print_r( $modelo, true)
        ));
        //Quitar de sesion el pregunta borrado para la proxima vez...
        sesion::set( 'pregunta.borrado', null);
      } else {
        echo 'No se ha podido guardar el pregunta de la sesion.';
      }//if
    } else {
      $bien= $modelo->cargar( 'ZA000005');
      if ($bien) {
        depurar( array( 
          'modelo.cargado' => print_r( $modelo, true)
        ));
        if ($modelo->eliminar()) {
          depurar( array( 
            'modelo.borrado' => print_r( $modelo, true)
          ));
          //Guardar en sesion el pregunta borrado para la proxima vez...
          sesion::set( 'pregunta.borrado', $modelo);
        } else {
          echo 'No se ha podido eliminar el pregunta de la BD.';
        }//if
      } else {
        echo 'No se ha podido cargar el pregunta de la BD.';
      }//if
    }//if
  }//accion_borrardemo
  
}//class controlador_test
