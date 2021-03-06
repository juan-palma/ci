<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		
class Servicios extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('upload');
	}
	
	public $varFlash = 'flashHome';
	public $success = [];
	public $error = [];
	
	public $status = [];
	public $valores = [];
	public $errores = [];
	
	public function index(){
		isNoLogged();
		
		$encontrar = array("\r\n", "\n", "\r");
		$remplazar = '';		
		
		
		//Consulta - Registro - servicios
		$this->basic_modal->clean();
		$this->basic_modal->tabla = 'contenido';
		$this->basic_modal->campos = 'id_contenido, contenido_info';
		$this->basic_modal->condicion = array( "contenido_pagina" => 'servicios', "contenido_seccion" => 'registro' );
		
		$respuesta = $this->basic_modal->genericSelect('sistema');
		$union = [];
		if(is_array($respuesta) && count($respuesta) > 0){
			foreach($respuesta as $i=>$r){
				$clean = (isset($r) && property_exists($r, 'contenido_info')) ? str_replace($encontrar, $remplazar, $r->contenido_info) : '';
				$cleanObjecDB = ( is_object(json_decode($clean)) ) ? json_decode($clean) : new stdClass();
				$cleanObjecDB->id = $r->id_contenido;
				$union[$i] = $cleanObjecDB;
			}
		}
		$data['registroDB'] = $union;
		
		
		
		
		$data['titulo'] = "Registros - servicios";
		$data['actual'] = "registro_servicios";
		$data['varFlash'] = $this->varFlash;
		$this->load->view('admin/head2', $data);
		$this->load->view('admin/saveControl', $data);
		$this->load->view('admin/servicios', $data);
		$this->load->view('admin/footer2', $data);
	}
	
	public function registro($peticion = ''){
		isNoLogged();
		
		$encontrar = array("\r\n", "\n", "\r");
		$remplazar = '';		
		
		
		//Consulta - Registro - servicios
		$this->basic_modal->clean();
		$this->basic_modal->tabla = 'contenido';
		$this->basic_modal->campos = 'id_contenido, contenido_info';
		$this->basic_modal->condicion = array( "contenido_pagina" => 'servicios', "contenido_seccion" => 'registro' );
		
		$respuesta = $this->basic_modal->genericSelect('sistema');
		$union = [];
		if(is_array($respuesta) && count($respuesta) > 0){
			foreach($respuesta as $i=>$r){
				$clean = (isset($r) && property_exists($r, 'contenido_info')) ? str_replace($encontrar, $remplazar, $r->contenido_info) : '';
				$cleanObjecDB = ( is_object(json_decode($clean)) ) ? json_decode($clean) : new stdClass();
				$cleanObjecDB->id = $r->id_contenido;
				$union[$i] = $cleanObjecDB;
			}
		}
		$data['registroDB'] = $union;
		
		
		
		//Consulta - Valor - servicios
		$this->basic_modal->clean();
		$this->basic_modal->tabla = 'contenido';
		$this->basic_modal->campos = 'id_contenido, contenido_info';
		$this->basic_modal->like = array("contenido_info" => '"url":"'.$peticion.'"');
		$this->basic_modal->condicion = array( "contenido_pagina" => "servicios",  "contenido_seccion" => "registro");
		
		$respuesta2 = $this->basic_modal->genericSelect('sistema');
		if(is_array($respuesta2) && count($respuesta2) > 0){
			$clean2 = ($respuesta2[0] && property_exists($respuesta2[0], 'contenido_info')) ? str_replace($encontrar, $remplazar, $respuesta2[0]->contenido_info) : '';
			$cleanObjecDB2 = ( is_object(json_decode($clean2)) ) ? json_decode($clean2) : new stdClass();
			$cleanObjecDB2->id = $respuesta2[0]->id_contenido;
			$data['articuloDB'] = $cleanObjecDB2;
		}
		
		
		
		$data['titulo'] = "Registros - servicios";
		$data['actual'] = "registro_servicios";
		$data['varFlash'] = $this->varFlash;
		$this->load->view('admin/head2', $data);
		$this->load->view('admin/saveControl', $data);
		$this->load->view('admin/servicios', $data);
		$this->load->view('admin/footer2', $data);
		
	}
		
		
		
	
	
	private function loadFiles($s, $it, $a, $c){
		$this->upload->initialize($c);
		
		$todasCargaron = true;
		$rutaImagenes = [];
		$count = count($a);
		$this->valores[$s][$it] = [];
		
		for ($i = 0; $i < $count; $i++) {
			if( !isset($_POST[$s.$i.'_'.$it]) ){
				if(isset($_FILES[$s.$i.'_'.$it])){
					if($_FILES[$s.$i.'_'.$it]['name'] !== "" && $_FILES[$s.$i.'_'.$it]['error'] == 0){
						
						if ( ! $this->upload->do_upload($s.$i.'_'.$it) ){
							$todasCargaron = false;
							$this->status = 'error';
							$this->errores[] =  $this->upload->display_errors();
							$rutaImagenes[]['file_name'] = '';
							$this->valores[$s][$it][$i] = '';
						} else{
							$result = $this->upload->data();
							$rutaImagenes[] = $result;
							$this->valores[$s][$it][$i] = $result['file_name'];
						}
					} else{
						$rutaImagenes[]['file_name'] = '';
						$this->valores[$s][$it][$i] = '';
					}
				} else{
					$rutaImagenes[]['file_name'] = '';
					$this->valores[$s][$it][$i] = '';
				}
			} else{
				$rutaImagenes[]['file_name'] = $_POST[$s.$i.'_'.$it];
				$this->valores[$s][$it][$i] = 'nop';
			}
		}
		
		if($todasCargaron === true){
			return $rutaImagenes;
		} else{
			return false;
		}
	}
	
	
	
	
	public function do_upload(){
		$this->status = 'ok';
		
		// REGISTROS
		//::::::  Seccion para procesar informacion de SERVICIOS :::::
		$this->valores['registro'] = [];
		
		$config['upload_path']		= FCPATH.'assets/public/img/servicios/registros/';
		$config['allowed_types']	= 'gif|jpg|jpeg|png|svg|svg+xml';
		$config['max_size']			= 2048;
		$config['overwrite']		= true;
		
		
		//subir fotos de imagenes globales de titulo
		$loadPortada = $this->loadFiles('base', 'titulo_fondo', ['null'], $config);
		
		//subir fotos de registro
/*
		if( isset($_POST['registros']['bloque']) ){
			$loadImagen = $this->loadFiles('bloque', 'imagen', $_POST['registros']['bloque'], $config);
		} else{
			$loadImagen = [];
		}
*/
		
		
		//subir logos de galeria
		if( isset($_POST['galeria']['foto']) ){
			$loadFoto = $this->loadFiles('galeria', 'foto', $_POST['galeria']['foto'], $config);
		} else{
			$loadFoto = [];
		}
		
		
		
		//subir logos de galeria titulo
		if( isset($_POST['galeriaT']['fotoT']) ){
			$loadFotoT = $this->loadFiles('galeriaT', 'fotoT', $_POST['galeriaT']['fotoT'], $config);
		} else{
			$loadFotoT = [];
		}
		


		if($loadFoto !== false){
			//Datos de la seccion Nosotros.
// 			$linea = '{"titulo_general":"'.$_POST['registros']['titulo'].'", "nombre":"'.$_POST['registros']['nombre'].'", "url":"'.url_title($_POST['registros']['url']).'", "intro":"'.$_POST['registros']['intro'].'", "pie":"'.$_POST['registros']['pie'].'", "titulo_fondo":"'.$loadPortada[0]['file_name'].'", "bloques":[';
			$linea = '{"titulo_general":"'.$_POST['registros']['titulo'].'", "nombre":"'.$_POST['registros']['nombre'].'", "url":"'.url_title($_POST['registros']['url']).'", "intro":"'.$_POST['registros']['intro'].'", "pie":"'.$_POST['registros']['pie'].'", "bloques":[';
			
// 			$linea = '{"titulo_general":"'.$_POST['registros']['titulo'].'", "video":"'.$_POST['registros']['video'].'", "video_portada":"'.$loadPortada[0]['file_name'].'", "enlace":"'.url_title($_POST['registros']['enlace']).'", "bloques":[';
			
			if( isset($_POST['registros']['bloque']) ){
				foreach ($_POST['registros']['bloque'] as $i=>$v) {
					if($i !== 0){ $linea .= ', '; }
					$linea .= '{"titulo1":"'.$v['titulo1'].'", "texto1":"'.$v['texto1'].'"}';
				}
			}
			
			$linea .= '], "galeria":[';
			if( isset($_POST['galeria']['foto']) ){
				foreach ($_POST['galeria']['foto'] as $i=>$v) {
					if($i !== 0){ $linea .= ', '; }
					$linea .= '{"foto":"'.@$loadFoto[$i]['file_name'].'"}';
				}
			}			
			
			$linea .= '], "galeriaT":[';
			if( isset($_POST['galeriaT']['fotoT']) ){
				foreach ($_POST['galeriaT']['fotoT'] as $i=>$v) {
					if($i !== 0){ $linea .= ', '; }
					$linea .= '{"fotoT":"'.@$loadFotoT[$i]['file_name'].'"}';
				}
			}
			
			$linea .= ']}';
			
			
			//consultar si existe un registro con valores para HOME-SECCIONES para saber si interta nuevo registro o actualizar el actual.
			//Consulta - HOME-SECCIONES
			$this->basic_modal->clean();
			$this->basic_modal->tabla = 'contenido';
			$this->basic_modal->campos = 'id_contenido';
			$this->basic_modal->condicion = array( "id_contenido" => $_POST['registros']['id'] );
			
			$respuesta = $this->basic_modal->genericSelect('sistema');
			
			//Insertar los valores en la base de datos
			//Consulta
			$this->basic_modal->clean();
			$this->basic_modal->tabla = 'contenido';
			
			if(count($respuesta) > 0){
				//Consulta UPDATE servicios
				$this->basic_modal->condicion = array('id_contenido', $_POST['registros']['id']);
				$valores = array('contenido_info' => $linea);
				$update = $this->basic_modal->genericUpdate('sistema', $valores);
			} else{
				//Consulta INSERT servicios
				$valores = array( 'contenido_info' => $linea, 'contenido_pagina' => 'servicios', 'contenido_seccion' => 'registro', 'contenido_user' => $_POST['userID']);
				$insert = $this->basic_modal->genericInsert('sistema', $valores);
				$this->valores['registro']['id'] = $insert;
			}
		} else{
			$this->errores[] = 'No se cargaron todas las imágenes de la sección de nosotros.';
		}
		
		
		
		
		
		
		
		
		//Fin de la operación y retorno de la respuesta JSON a la consulta.
		echo( json_encode(['status' => $this->status, 'valores' => $this->valores, 'errores' => $this->errores]) );
		$this->cleanVar();
	}
	
	
	
	
	
	
	private function clean(){
		unset(
	        $_SESSION['formData']
		);
		
		redirect(base_url('admin/home'));
	}
	
	
	private function cleanVar(){
		$this->status = [];
		$this->valores = [];
		$this->errores = [];
	}
	
	
	public function delReg($id = ''){
		isNoLogged();
		
		if($id !== ''){
			$this->basic_modal->clean();
			$this->basic_modal->tabla = 'contenido';
			$valores = array('id_contenido' => $id);
			$insert = $this->basic_modal->genericDelete('sistema', $valores);
			
			header('Location: '. base_url('admin/servicios'));
		}
	}
	
}



