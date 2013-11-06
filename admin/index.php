<?
  include('funciones.php');
  session_start();
  if(!$_SESSION['Logueado']){
    header('location:login.php');
    return;
  }
  if(isset($_REQUEST['logout']))
  {
    $_SESSION['Logueado'] = false;
    header('location:login.php');
    return;
  }
  
?>
<html>
  <head>
    <title>.:: Mundo de Fantas&iacute;a ::. Peluches</title>
    <link rel="stylesheet" href="styles.css" type="text/css"/>
    <script src="http://codeorigin.jquery.com/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="helper.js?a=<?=md5(rand(0,1000))?>" ></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
    <img style="float:left;" src="../resources/logo_admin.jpg" />
    <p id="welcome-text">Panel de Control<br/>Bienvenidos al administrador de peluchesfantasia.com</p>
    <table id="item-table">
      <tr>
	<td class="item-selected" onclick="javascript:changeContent('user')" >Usuario</td>
	<td class="item-unselected" onclick="javascript:changeContent('subcategory')" >Subcategoría</a></td>
	<td class="item-unselected" onclick="javascript:changeContent('product')" >Productos</td>	
      </tr>
    </table>
    <a id="logout" href="index.php?logout" />Salir</a>
    <table id="action-table">
      <tr>
	<td class="action-unselected" onclick="javascript:edit()" >Editar</td>
	<td class="action-unselected" onclick="javascript:remover()" >Borrar</td>
	<td class="action-unselected" onclick="javascript:add()" ><p id="add-link">Agregar Usuario</p></td>
      </tr>
    </table>
    <iframe id="content" src="content.php?show=user" scrolling="no" ></iframe>
  </body>
</html>
