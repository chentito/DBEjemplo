<?php
/**
 * Acceso a la base de datos MySQL
 *
 * @author CaViReSa
 */

//==============================================================================
// Clase que maneja la conexion a la base de datos 
//============================================================================== 
 
class DB{
        
    /* Datos de acceso */
    var $usuarioDB = '';
    var $contrasenaDB = '';
    var $nombreDB = '';
    var $servidorDB = '';
    var $conn = '';

    /* Manejo de datos */
    var $EOF = false;
    var $resultSet = null;
    
    /* Control de errores */
    var $logerror = 'errores_db.log';
    var $error = '';
    
    public function DB(){
        /* Establece los datos de conexion */
        include_once './conf.php';        
        $this->usuarioDB = _DBUSER_;
        $this->contrasenaDB = _DBPASSWORD_;
        $this->nombreDB = _DBDATABASE_;
        $this->servidorDB = _DBHOST_;
        
        try{
                $this->conexion();
            }catch( Exception $ex ) {
                $this->log_console( $ex->getMessage() );
                die( $ex->getMessage() );
        }
        
    }
    
    /* Funcion que intenta la conexion a la base de datos con los accesos proporcionados */
    private function conexion(){
        /* Intenta establecer conexion a la base de datos */
        $conn = @mysql_connect( $this->servidorDB , $this->usuarioDB , $this->contrasenaDB );
        if( !$conn ){
            throw new Exception( 'Error de conexion al servidor de base de datos' );
        }else{
            $db_conn = @mysql_select_db( $this->nombreDB , $conn );
            if( !$db_conn ){
                throw new Exception( 'Error de conexion a la base de datos' );
            }
        }
        
        $this->conn = $conn;
        return $conn;
    }
    
    public function consulta( $sql ){
        try{
                $this->ejecuta( $sql );
                
                if( is_bool( $this->resultSet ) ){ return $this->resultSet; }
                if( is_resource( $this->resultSet ) ){
                    $recordSet = '';
                    while($fila = mysql_fetch_assoc( $this->resultSet )){
                        $recordSet[] = $fila;
                    }
                    
                    $rSet = new RS( $recordSet );                    
                    return $rSet;
                }
                
            }catch( Exception $ex ) {
                $this->log_console( $ex->getMessage() );
                die( $ex->getMessage() );
        }
    }    
   
    /* Libera memoria utilizada por el resultado */
    public function libera(){
        @mysql_free_result( $this->resultSet );
    }
    
    /* Ejecucion de la consulta */
    private function ejecuta( $sql ){
        /* Intenta ejecutar el comando recibido */
        $this->resultSet = @mysql_query( $sql , $this->conn );
        if( !$this->resultSet ){
            $this->error = mysql_error();
            throw new Exception( 'Error al ejecutar comando SQL:[' . mysql_errno() . '] ' . mysql_error() );
        }        
    }
    
    /* Log errores */
    private function log_console( $msj ){
        $error = "[" . date("Ymd/His") . "]-Descripcion: " . $msj . "\n";
        $f = fopen( $this->logerror , 'a+' );
        fwrite( $f, $error );
        fclose( $f );        
    }
    
}/* Fin clase DB */


//==============================================================================
// Clase que lleva el control del recordset regresado al ejecutar un DML
//==============================================================================
class RS{
    
    /* Variables */
    var $rs = null;
    var $registros = 0;
    var $EOF = false;
    var $indiceRecorrido = 0;
    var $datos = null;
    
    public function RS( $datos ){
        $this->rs = $datos;
        $this->registros = count($datos);
        $this->campos();
    }
    
    public function campos(){
        $current = $this->rs[$this->indiceRecorrido];
        foreach($current AS $indice=>$valor){
            $this->datos[$indice] = $valor; 
        }
    }
    
    public function siguiente(){
        $this->indiceRecorrido++;
        if($this->indiceRecorrido == $this->registros){
            $this->EOF = true;
        }
    }
    
}
