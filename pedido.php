<?php
session_start();

//   echo '<pre>';
//   print_r($_SESSION['pedido']);
//   echo '</pre>';
  // echo header('Content-Type: text/xml');

function cartInfo()
{
  $size = 0;
  $total = 0.0;
  foreach($_SESSION['pedido'] as $key => $product)
  {
    $total+=$product['cantidad']*$product['precio'];
    $size+=$product['cantidad'];
  }
  $buffer='<p id="static-bar-text" >ESTADO DE TU CARRITO <a href="carrito.php?carrito=show" ><img border="0" src="resources/carrito-para-barra-de-estado.png" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<span id="static-bar-text2" >Cantidad de Productos: '.$size.'&nbsp;&nbsp;&nbsp; Monto: $'.$total.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:clearProduct(\'-1\');" >Limpiar</a></p>';
  return $buffer;
}


if(isset($_POST['action']))
{
  if($_POST['id']=="-1")
    unset($_SESSION['pedido']);
  else
    unset($_SESSION['pedido'][$_POST['id']]);
}
else
{
  if(!isset($_SESSION['pedido'][$_POST['id']]))
  {
    $product = array();
    $product['nombre'] = $_POST['nombre'];
    $product['descripcion'] = $_POST['descripcion'];
    $product['precio'] = $_POST['precio'];
    $product['cantidad'] = $_POST['cantidad'];
    $product['categoria'] = $_POST['categoria'];
    $_SESSION['pedido'][$_POST['id']] = $product;
  }
  else
  {
    if(isset($_POST['replace']))
      $_SESSION['pedido'][$_POST['id']]['cantidad'] = $_POST['cantidad'];
    else
      $_SESSION['pedido'][$_POST['id']]['cantidad'] += $_POST['cantidad'];
  }
}
echo cartInfo();

?>