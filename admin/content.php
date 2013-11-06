<?
include('funciones.php');
session_start();
if(!isset($_SESSION['Logueado']))
{
  return;
}
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script src="http://codeorigin.jquery.com/jquery-1.10.2.min.js" type="text/javascript"></script>    
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/themes/base/jquery-ui.css" type="text/css" media="all" />
    <link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />

    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/jquery-ui.min.js" type="text/javascript"></script>
    
    <link rel="stylesheet" href="content.css" type="text/css"/>
    <script type="text/javascript" src="helper.js" ></script>
<script language="JavaScript" type="text/javascript">
	$(document).ready(function(){
	  $("#subcategory-list").sortable({
	    update:function(){
	      var url = 'pedido.php?'+$("#subcategory-list").sortable("serialize");
	      $.post(url,{request:"neworder"},function(data){
		 alert(data);
	      });
	    }
	  });
	});
    </script>
  </head>
  <body>
    <div id="main-menu" ><? showTable(); ?></div>
  </body>
</html>
