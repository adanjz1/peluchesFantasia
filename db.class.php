<?php
if (!defined('__DB_CLASS_')) {
	define('__DB_CLASS_',true);

// Libreria Manejo de BASE DE DATOS
Class dbConnect {
    var $dbh, $dbname, $conectado;
    function dbConnect ($host=null, $username = null, $password = null, $dbname = null) {
    	// includes necesarios
	    include_once ('db.ini.php');
    	// FIN includes necesarios

        if ($host==null) { $host = __DB_PARAMETER_HOST_; }
        if ($username==null) { $username = __DB_PARAMETER_USER_; }
        if ($password==null) { $password = __DB_PARAMETER_PASSWORD_; }
        if ($dbname==null) { $dbname = __DB_PARAMETER_NAME_; }

        
        $this->dbh = mysql_connect($host,$username,$password);

        if (!$this->dbh) {
            header ('Location: /errores.php');
            exit;
        }

        if (mysql_select_db($dbname, $this->dbh)) {
	        $this->dbname = $dbname;
	        $this->conectado = true;
        } else {
	        $this->dbname = null;
	        $this->conectado = false;
        }
        
        
    } // Fin funcion dbConnect (constructor)

    function Disconnect () {
		mysql_close($this->dbh);
        $this->conectado = false;
    } // Fin funcion Disconnect
} // Fin clase dbConnect

// Se crea la conexion global
global $DBCONNECT;
$DBCONNECT = new dbConnect();

Class dbDML {
	var $dbh, $err, $sth, $QueryString, $QueryOriginal, $binds;

	function dbDML ($query = '', $binds = array(), $dbh = null, $track = 0) {
		// inicializo las properties ppales
        $this->QueryOriginal = $query;
        $this->binds = $binds;

		if ($query != '') { // existe la opcion de usar Parse y Execute por separado
	        $this->Parse($query, $dbh);
	        $this->Execute($binds);
	        return $this->err;
    	}
    } // Fin Funcion dbDML (constructor)

    function Parse ($query, $dbh = null) {
        $this->dbh = &$GLOBALS['DBCONNECT']; // la conexion a la base es una referencia a un objeto dbConnect
        $this->QueryOriginal = $query;
        $this->QueryString = str_replace('[[:space:]]+',' ',trim($query));
        $this->err = 0;
        $this->sth = false;

        if ($dbh != null) { $this->dbh = &$dbh; } // la conexion a la base es una referencia a un objeto dbConnect
    } // Fin Funcion Parse

    function Execute ($binds = array()) {
    	// Includes necesarios
	   //include_once ('constantes.php');
    	// FIN Includes necesarios
        $this->binds = $binds;
		$query = $this->BindQuery();
        $this->err = !($this->sth = mysql_query($query, $this->dbh->dbh));
        
        // DEBUG
        //$this->Show();

        if ($this->err) {

        
// DEBUG
//include_once ('emails.ini.php');
            $MENSAJE='
Empresa : '._Main_Empresa_.' 
Fecha	: '.date('d/m/Y H:i:s').'
Error en: '.$_SERVER['REQUEST_URI'].'
Servidor: '.$_SERVER['SERVER_ADDR']."

Descripcion Error: ".mysql_errno($this->dbh->dbh).': '.mysql_error($this->dbh->dbh)."
            
Query: 
".$this->QueryString."
            
Query completo: 
$query

Session: 
".str_replace(';',"\n",session_encode());

//            if (!__ENV_DESARROLLO)
//                mail(__EMAIL_Errores_SQL_,'Error SQL - '.$_SERVER['PHP_SELF'], $MENSAJE,'From: Administraci�n '._Main_Empresa_.' <'.__EMAIL_Tecnologia_.'>');
//            else
                echo nl2br("<FONT COLOR=\"#ff0000\">$MENSAJE</FONT>");

        }
        return $this->err;
    } // Fin funcion Execute

    function Free () {
    	if ($this->sth !== false & $this->sth !== true)
	    	mysql_free_result ($this->sth);
    } // Fin funcion Free

    function Show ($mostrar_en_publicado=false) {
	   if (__ENV_DESARROLLO || $mostrar_en_publicado) {
	        echo ($mostrar_en_publicado ? '<!--' : '').'Query: '.htmlspecialchars($this->BindQuery()).";\n<BR>\n".($mostrar_en_publicado ? '-->' : '');
	    }
    } // Fin funcion Show

    function BindQuery () { // devuelve el string del Query pero con las binds reemplazadas

		$query = $this->QueryString;
        if (is_array($this->binds)) {
	        $rBinds = array_reverse($this->binds,true);
            foreach ($rBinds as $k => $valor) {
                if ($valor === null || $valor === '' || $valor === false)
                    $valor = 'NULL';
	            else
	                if (preg_match("/[^0-9]/",$valor))
	                    $valor = "'$valor'";

	            $query=str_replace(":p$k",$valor,$query);
	        }
        }
        return $query;
    } // Fin funcion BindQuery
    
    function LastInsertedId() {
        return mysql_insert_id($this->dbh->dbh);
    }
    
} // Fin clase dbDML

Class dbQuery extends dbDML {
    var $result, $NumRow;

    function dbQuery ($query = '', $binds = array(), $dbh = null) {
		$this->dbDML ($query, $binds, $dbh); // llamamos al constructor del padre
        $this->result = $this->NumRow = null;
    } // Fin funcion dbQuery (constructor)

    function NextValue ($cant = 1) {
        $i = 0;

        $this->result = array();
        while (($i < $cant) && $this->sth && ($this->result = mysql_fetch_array($this->sth, MYSQL_ASSOC))) {
        	++$i;
        }

        $resultUcase = array();

        if ($i != $cant)
        	$this->result = array();

        foreach ($this->result as $key=>$val) {
            $resultUcase[strtoupper($key)] = $val;
        }

        return $this->result = $resultUcase;
    } // Fin funcion NextValue
	
    function NumRows() {
        $this->NumRow = mysql_num_rows($this->sth);
    	return $this->NumRow;
    }

    function Free() {
        parent::Free(); // llamamos a la del padre
    	$this->NumRow = null;
    }
} // Fin clase dbQuery

Class dbTransaction extends dbDML {
    var $status, $RowCount;
    function dbTransaction ($query=null, $binds=array(), $dbh=null) {
        $this->status = 'Open';
        $this->RowCount = null;
		$this->dbDML ($query, $binds, $dbh); // llamamos al constructor del padre
    } // Fin funcion dbTransaction (constructor)

    function DoTransaction ($query=null, $binds=array(), $dbh=null) {
        $this->Parse ($query, $dbh);
        $this->Execute ($binds);
    } // Fin funcion DoTransaction

    function Commit () {
		// se usa autocommit
    } // Fin funcion Commit

    function RollBack () {
		// se usa autocommit
    } // Fin funcion RollBack

    function AffectedRows() {
        $this->RowCount = mysql_affected_rows();
    	return $this->RowCount;
    } // Fin funcion AffectedRows

    function Free () {
        parent::Free();
        $this->status = 'Close';
    	$this->RowCount = null;
    } // Fin funcion Free
} // Fin clase dbTransaction

} // Fin __DB_CLASS_
?>
