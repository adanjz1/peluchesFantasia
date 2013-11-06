<?

include('db.class.php');
session_start();
if(!isset($_SESSION['pedido']))
{
    $_SESSION['pedido'] = array();
}
if(!isset($_SESSION['idUsuario']))
{
  srand((double)microtime()*1000000);
  $random =  rand(0,10000);
  srand((double)microtime()*1000000);
  $random .=  rand(0,1000000);
  $_SESSION['idUsuario'] = $random;
}

function item($product)
{
  $buffer='<table ><tr><td><p class="product-code" >C&oacute;digo: '.$product['code'].'</p></td></tr>';
  $buffer.='<tr><td><p class="product-name" >'.$product['name'].'</p></td></tr></table>';
  $buffer.='<div class="product-area" >';
  $buffer.='<a href="resources/thumbnail/elemento-'.$product['image'].'" rel="lightbox" title="'.$product['name']." - ".$product['description'].'" ><img class="product-img" src="resources/thumbnail/elemento-'.$product['image'].'" /></a><br/>';
  $buffer.='<table class="product-info" >';
  $buffer.='<tr><td class="product-desc" >'.$product['description'].'</td><td class="product-price" >$'.$product['price'].'<br/>';
  if($product['video']!="" && $product['video']!="NULL")
    $buffer.='<a href="'.$product['video'].'" rel="lightbox[social 480 380]" title="'.$product['name']." - ".$product['description'].'" ><img src="resources/ico_video.png" /></a>';
  $buffer.='</td></tr>';
  $buffer.='<tr><td class="product-count" ><input type="text" id="product-'.$product['code'].'" size="2" /><a href="carrito.php?carrito=show" ><img style="position:relative;top:5px;left:5px" src="resources/ico_carrito_producto.png" /></a></td>';
  $buffer.='<td><input class="product-button" type="image" src="resources/boton_agregar.gif" onclick="javascript:confirmar(\''.$_SESSION['idUsuario'].'\',\''.$product['code'].'\',\''.$product['name'].'\',\''.$product['description'].'\',\''.$product['price'].'\',\''.$product['category'].'\');" /></td></tr>';
  $buffer.='</table>';
  $buffer.='</div>';
  return $buffer;
}

function show($list)
{
  $buffer='<table style="background-color:#FFFFFF;position:relative;top:30px" ><tr>';
  if(is_array($list)) {

    $columns=3;
    $count=0;
    foreach($list as $key=>$product) {
      if($count==$columns)
      {
        $buffer.='</tr><tr>';
        $count=0;
      }
      $item = item($product);
      $buffer.='<td style="width:280px;height:310px" >'.$item.'</td>';
      $count++;
    }
  }
  $buffer.='</tr></table>';
  return $buffer;
}
function showSearchedProducts()
{
  if(isset($_GET['search']))
  {
    $type = $_GET['search'];
    if($type=="category")
    {
      $id = $_GET['id'];
      echo pathMenu();
      echo showSubcategorys();
      $query = "select * from subcategory where idcategory='{$id}'";
      $subcategories = new dbQuery($query);
      while($subcategories->NextValue())
      {
	$query = "select * from product where subcategory='{$subcategories->result['ID']}'";
	$products = new dbQuery($query);
	while($products->NextValue()) 
	{
	  $list[] = array(
	    'code' => $products->result['CODE'],
	    'name' => $products->result['NAME'],
	    'description' => $products->result['DESCRIPTION'],
	    'price' => $products->result['PRICE'],
	    'image' => $products->result['IMAGE'],
	    'video' => $products->result['VIDEO'],
	    'category' => $subcategories->result['NAME']
	  );
	}
      }
    }
    else if($type=="subcategory")
    {
      $id = $_GET['id'];
      echo pathMenu();
      echo showSubcategoryName();
      $subcat  = new dbQuery("select name from subcategory where id='{$id}'");
      if($subcat->NextValue())
      {
	$subcatname = $subcat->result['NAME'];
      }
      $query = "select * from product where subcategory='{$id}'";
      $products = new dbQuery($query);
      while($products->NextValue()) 
      {
	$list[] = array(
	  'code' => $products->result['CODE'],
	  'name' => $products->result['NAME'],
	  'description' => $products->result['DESCRIPTION'],
	  'price' => $products->result['PRICE'],
	  'image' => $products->result['IMAGE'],
	  'video' => $products->result['VIDEO'],
	  'category' => $subcatname
	);
      }    
    }
    else
    {
      $query = "select * from product p where code='{$_GET['search']}'";
      $products = new dbQuery($query);
      if($products->NumRows()==0)
      {
	      $query = "select * from product p where name like '%".$_GET['search']."%' order by p.id ASC";
	      $products = new dbQuery($query);
      }

      while($products->NextValue()) 
      {
        $subcat  = new dbQuery("select name from subcategory where id='{$products->result['SUBCATEGORY']}'");
        if($subcat->NextValue())
        {
          $subcatname = $subcat->result['NAME'];
        }
        
        $list[] = array(
          'code' => $products->result['CODE'],
          'name' => $products->result['NAME'],
          'description' => $products->result['DESCRIPTION'],
          'price' => $products->result['PRICE'],
          'image' => $products->result['IMAGE'],
          'video' => $products->result['VIDEO'],
          'category' => $subcatname
        );    
      }
    }
    echo show($list);
  }
}
function showSubcategoryName()
{
  $query = "select * from subcategory where id='{$_GET['id']}'";
  $subcategories = new dbQuery($query);
  while($subcategories->NextValue())
  {
      echo '<p id="menu-subcategory-title" />'.$subcategories->result['NAME'].'</p>';
  }
}
function pathMenu()
{
  $buffer='<a class="path-menu" href="productos.php?products=show"  >productos<a/>';
  $type = $_GET['search'];
  if($type=="subcategory")
  {
    $query = "select * from subcategory where id='{$_GET['id']}'";
    $subcategories = new dbQuery($query);
    while($subcategories->NextValue())
    {
      $query = "select * from category where id='{$subcategories->result['IDCATEGORY']}'";
      $categories = new dbQuery($query);
      while($categories->NextValue())
      {
	$buffer.='<span class="path-menu"> > </span><a class="path-menu" href="productos.php?products=show&search=category&id='.$categories->result['ID'].'"  >'.htmlentities($categories->result['NAME']).'<a/>';
      }
      $buffer.='<span class="path-menu"> > '.$subcategories->result['NAME'].'</span>';
    }
  }
  else if($type=="category")
  {
      $query = "select * from category where id='{$_GET['id']}'";
      $categories = new dbQuery($query);
      while($categories->NextValue())
      {
	$buffer.='<span class="path-menu"> > <a class="path-menu" href="productos.php?products=show&search=category&id='.$categories->result['ID'].'"  >'.htmlentities($categories->result['NAME']).'<a/></span>';
      }
  }
  $buffer.='<br/>';
  return $buffer;
}
function showSubcategorys()
{
    //<PARCHE
    $query = "select * from category where id='{$_GET['id']}'";
    $categories = new dbQuery($query);
    while($categories->NextValue())
    {
      echo '<a href="productos.php?products=show&search=category&id='.$categories->result['ID'].'" ><img src=resources/category/'.$categories->result['IMAGE'].' /></a>';
    }
    //PARCHE>

    $query = "select * from subcategory s where idcategory='{$_GET['id']}'";
    $result = new dbQuery($query);
    while($result->NextValue()) {
      $list[] = array('id' => $result->result['ID'],'name' => $result->result['NAME']);
    }
    if(isset($list))
    {
      $buffer='<table style="position:relative;top:14px" ><tr>';
      $columns=3;
      $count=0;
      foreach($list as $key => $val)
      {
	$nl = ($count%$columns)==0;
	if($nl && $count!=0)
	{
	  $buffer.='</tr><tr>';
	}
	$buffer.='<td id="menu-subcategory-cell" ><a id="menu-subcategory-text" href="productos.php?products=show&search=subcategory&id='.$val['id'].'" >'.$val['name'].'</a></td>';
	$count++;
      }
    }
    $buffer.='</tr></table>';
    echo $buffer;
}

function showOrdered()
{
  if(count($_SESSION['pedido']))
  {
    $buffer;
    if(isset($_GET['carrito']) && $_GET['carrito']=='fill')
    {
      $buffer='<div id="cart-table">';
      $buffer.='<img src="resources/rotulo_formulario.png"/>';
      $buffer.='<form name="orderedform" action="carrito.php?carrito=send" method="post" onsubmit="checkOrdered(event);">'; 
      $buffer.='<table style="position:relative;width:493px;left:20px;">';
      
      $buffer.='<tr><td><table><tr><td><label class="form-text" >Nombre y Apellido</label><br/><input type="text" style="width:227px;" name="name" /></td>';
      $buffer.='<td><label class="form-text" >Raz\F3n Social</label><br/><input style="width:227px;" type="text" name="socialreason" /></td></tr></table></td></tr>';
      
      $buffer.='<tr><td><table><tr><td><label class="form-text" >Domicilio</label><br/><input style="width:227px;" type="text" name="address" /></td>';
      $buffer.='<td><label class="form-text" >Localidad</label><br/><input style="width:227px;" type="text" name="city" /></td></tr></table></td></tr>';
      
      $buffer.='<tr><td><table><tr><td><label class="form-text" >Provicia</label><br/><input style="width:125px;" type="text" name="state" /></td>';
      $buffer.='<td><label class="form-text" >C.P.</label><br/><input style="width:46px;" type="text" name="cp" /></td>';
      $buffer.='<td><label class="form-text" >Tel\E9fono</label><br/><input style="width:101px;" type="text" name="tel" /></td>';
      $buffer.='<td><label class="form-text" >Fax</label><br/><input style="width:101px;" type="text" name="fax" /></td></tr></table></td></tr>';
      
      $buffer.='<tr><td><table><tr><td><label class="form-text" >E-mail</label><input style="width:160px;" type="text" name="email" /></td>';
      $buffer.='<td><label class="form-text" >Web</label><input style="width:160px;" type="text" name="web" /></td>';
      $buffer.='<td><label class="form-text" >Categor\EDa I.V.A.</label><input style="width:94px;" type="text" name="cativa" /></td></tr></table></td></tr>';
      
      $buffer.='<tr><td><table><tr><td><label class="form-text" >C.U.I.T.</label><br/><input style="width:131px;" type="text" name="cuit" /><br/>';
      $buffer.='<label class="form-text" >Transporte</label><br/><input style="width:227px;" type="text" name="transport" /></td>';
      $buffer.='<td><label class="form-text" >Comentario</label><br/><textarea style="width:226px;height:60px;" name="comment" ></textarea></td></tr></table></td></tr>';
      $buffer.='</table>';
      $buffer.='</div>';
      $buffer.=showOrderedProducts(false);
      $buffer.='<input type="checkbox" name="savedata" /><label class="form-text">Guardar Datos</label><br/>';
      $buffer.='<table>';
      $buffer.='<tr><td><label class="form-text" >Nombre de Usuario</label></td><td><input type="text" name="username"/></td></tr>';
      $buffer.='<tr><td><label class="form-text" >Contrase\F1a</label></td><td><input type="password" name="password"/></td></tr>';
      $buffer.='<tr><td><label class="form-text" >Confirmar Contrase\F1a</label></td><td><input type="password" name="cpassword"/></td></tr>';
      $buffer.='</table><br/>';
      $buffer.='<input type="image" src="resources/boton_enviar_pedido.png" value="submit" onclick="checkOrdered(event);"></input>';
      $buffer.='<a style="position:relative;left:30px" href="productos.php" ><img src="resources/cancelar_pedido.png" /></a>';
      $buffer.='</form>';
      echo $buffer;
    }
    else if(isset($_GET['carrito']) && $_GET['carrito']=='send')
    {
//      prddint '<pre>_POST '; print_r($_POST); print '</pre>';
      if($_POST['savedata']=='on')
      {
        $error = saveClientData();
        if(!empty($error))
        {
          $buffer='<div id="cart-table">';
          $buffer.='<img src="resources/rotulo_formulario.png"/>';
          $buffer.='<form name="orderedform" action="carrito.php?carrito=send" method="post" onsubmit="checkOrdered(event);">'; 
          $buffer.='<table style="position:relative;width:493px;left:20px;">';
          $buffer.='<tr><td><table><tr><td><label class="form-text" >Nombre y Apellido</label><br/><input type="text" style="width:227px;" name="name" value="'.$_POST['name'].'" /></td>';
          $buffer.='<td><label class="form-text" >Raz\F3n Social</label><br/><input style="width:227px;" type="text" name="socialreason" value="'.$_POST['socialreason'].'" /></td></tr></table></td></tr>';
          $buffer.='<tr><td><table><tr><td><label class="form-text" >Domicilio</label><br/><input style="width:227px;" type="text" name="address" value="'.$_POST['address'].'" /></td>';
          $buffer.='<td><label class="form-text" >Localidad</label><br/><input style="width:227px;" type="text" name="city" value="'.$_POST['city'].'" /></td></tr></table></td></tr>';
          $buffer.='<tr><td><table><tr><td><label class="form-text" >Provicia</label><br/><input style="width:125px;" type="text" name="state" value="'.$_POST['state'].'" /></td>';
          $buffer.='<td><label class="form-text" >C.P.</label><br/><input style="width:46px;" type="text" name="cp" value="'.$_POST['cp'].'" /></td>';
          $buffer.='<td><label class="form-text" >Tel\E9fono</label><br/><input style="width:101px;" type="text" name="tel" value="'.$_POST['tel'].'" /></td>';
          $buffer.='<td><label class="form-text" >Fax</label><br/><input style="width:101px;" type="text" name="fax" value="'.$_POST['fax'].'" /></td></tr></table></td></tr>';
          $buffer.='<tr><td><table><tr><td><label class="form-text" >E-mail</label><input style="width:160px;" type="text" name="email" value="'.$_POST['email'].'" /></td>';
          $buffer.='<td><label class="form-text" >Web</label><input style="width:160px;" type="text" name="web" value="'.$_POST['web'].'" /></td>';
          $buffer.='<td><label class="form-text" >Categor\EDa I.V.A.</label><input style="width:94px;" type="text" name="cativa" value="'.$_POST['cativa'].'" /></td></tr></table></td></tr>';
          $buffer.='<tr><td><table><tr><td><label class="form-text" >C.U.I.T.</label><br/><input style="width:131px;" type="text" name="cuit" value="'.$_POST['cuit'].'" /><br/>';
          $buffer.='<label class="form-text" >Transporte</label><br/><input style="width:227px;" type="text" name="transport" value="'.$_POST['transport'].'" /></td>';
          $buffer.='<td><label class="form-text" >Comentario</label><br/><textarea style="width:226px;height:60px;" name="comment" >'.$_POST['comment'].'</textarea></td></tr></table></td></tr>';
          $buffer.='</table>';
          $buffer.='</div>';
          $buffer.=showOrderedProducts(false);
          $buffer.='<input type="checkbox" name="savedata" checked /><label class="form-text">Guardar Datos</label><br/>';
          $buffer.='<table>';
          $buffer.='<tr><td><label class="form-text" >Nombre de Usuario</label></td><td><input type="text" name="username"/></td><td><label class="form-text">Elija otro nombre de usuario</label></td></tr>';
          $buffer.='<tr><td><label class="form-text" >Contrase\F1a</label></td><td><input type="password" name="password"/></td></tr>';
          $buffer.='<tr><td><label class="form-text" >Confirmar Contrase\F1a</label></td><td><input type="password" name="cpassword"/></td></tr>';
          $buffer.='</table><br/>';
          $buffer.='<input type="image" src="resources/boton_enviar_pedido.png" value="submit" onclick="checkOrdered(event);"></input>';
          $buffer.='<a style="position:relative;left:30px" href="productos.php" ><img src="resources/cancelar_pedido.png" /></a>';
          $buffer.='</form>';
          echo $buffer;
        }
        else
        {
          sendOrder();
        }
      }
      else
      {
        sendOrder();
      }
    }
    else if(isset($_GET['carrito']) && $_GET['carrito']=='login')
    {
      $buffer='<img src="resources/registro.jpg" />';
      $buffer.='<form action="carrito.php?carrito=dologin" method="post" >';
      $buffer.='<table>';
      $buffer.='<tr><td><label class="form-text" >Nombre de Usuario</label></td><td><input type="text" name="username"/></td></tr>';
      $buffer.='<tr><td><label class="form-text" >Contrase\F1a</label></td><td><input type="password" name="password"/></td></tr>';
      $buffer.='</table><br/>';
      $buffer.='<input style="position:relative;top:10px;left:120px;" type="image" src="resources/boton_enviar_contacto.png" value="submit">';
      $buffer.='</form>';
      $buffer.='<br/><br/><a href="productos.php?carrito=recover" >Recuperar contrase\F1a</a>';
      echo $buffer;  
    }
    else if(isset($_GET['carrito']) && $_GET['carrito']=='dologin')
    {
      $query = new dbQuery('select * from client where username = :p0 and password = :p1',array($_POST['username'],$_POST['password']));
      if($query->NumRows()!=0 && $query->NextValue())
      {
        $buffer='<div id="cart-table">';
        $buffer.='<img src="resources/registro.jpg"/>';
        $buffer.='<form name="orderedform" action="carrito.php?carrito=send" method="post" onsubmit="checkOrdered(event);">'; 
        $buffer.='<table style="position:relative;width:493px;left:20px;">';
        $buffer.='<tr><td><table><tr><td><label class="form-text" >Nombre y Apellido</label><br/><input type="text" style="width:227px;" name="name" value="'.$query->result['NAME'].'" /></td>';
        $buffer.='<td><label class="form-text" >Raz\F3n Social</label><br/><input style="width:227px;" type="text" name="socialreason" value="'.$query->result['FIRMNAME'].'" /></td></tr></table></td></tr>';
        $buffer.='<tr><td><table><tr><td><label class="form-text" >Domicilio</label><br/><input style="width:227px;" type="text" name="address" value="'.$query->result['ADDRESS'].'" /></td>';
        $buffer.='<td><label class="form-text" >Localidad</label><br/><input style="width:227px;" type="text" name="city" value="'.$query->result['CITY'].'" /></td></tr></table></td></tr>';
        $buffer.='<tr><td><table><tr><td><label class="form-text" >Provicia</label><br/><input style="width:125px;" type="text" name="state" value="'.$query->result['STATE'].'" /></td>';
        $buffer.='<td><label class="form-text" >C.P.</label><br/><input style="width:46px;" type="text" name="cp" value="'.$query->result['CP'].'" /></td>';
        $buffer.='<td><label class="form-text" >Tel\E9fono</label><br/><input style="width:101px;" type="text" name="tel" value="'.$query->result['TELEPHONE'].'" /></td>';
        $buffer.='<td><label class="form-text" >Fax</label><br/><input style="width:101px;" type="text" name="fax" value="'.$query->result['FAX'].'" /></td></tr></table></td></tr>';
        $buffer.='<tr><td><table><tr><td><label class="form-text" >E-mail</label><input style="width:160px;" type="text" name="email" value="'.$query->result['EMAIL'].'" /></td>';
        $buffer.='<td><label class="form-text" >Web</label><input style="width:160px;" type="text" name="web" value="'.$query->result['WEB'].'" /></td>';
        $buffer.='<td><label class="form-text" >Categor\EDa I.V.A.</label><input style="width:94px;" type="text" name="cativa" value="'.$query->result['CATIVA'].'" /></td></tr></table></td></tr>';
        $buffer.='<tr><td><table><tr><td><label class="form-text" >C.U.I.T.</label><br/><input style="width:131px;" type="text" name="cuit" value="'.$query->result['CUIT'].'" /><br/>';
        $buffer.='<label class="form-text" >Transporte</label><br/><input style="width:227px;" type="text" name="transport" value="'.$query->result['TRANSPORT'].'" /></td>';
        $buffer.='<td><label class="form-text" >Comentario</label><br/><textarea style="width:226px;height:60px;" name="comment" ></textarea></td></tr></table></td></tr>';
        $buffer.='</table>';
        $buffer.='</div>';
        $buffer.=showOrderedProducts(false);
        $buffer.='<input type="image" src="resources/boton_enviar_pedido.png" value="submit" ></input>';
        $buffer.='<a style="position:relative;left:30px" href="productos.php" ><img src="resources/cancelar_pedido.png" /></a>';
        $buffer.='</form>';
        echo $buffer; 
      }
      else
      {
        $buffer='<img src="resources/registro.jpg" /><br/>';
        $buffer.='<label class="form-text">Usuario o contrase�a invalida</label>';
        $buffer.='<form action="carrito.php?carrito=dologin" method="post" >';
        $buffer.='<table>';
        $buffer.='<tr><td><label class="form-text" >Nombre de Usuario</label></td><td><input type="text" name="username"/></td></tr>';
        $buffer.='<tr><td><label class="form-text" >Contrase\F1a</label></td><td><input type="password" name="password"/></td></tr>';
        $buffer.='</table><br/>';
        $buffer.='<input style="position:relative;top:10px;left:120px;" type="image" src="resources/boton_enviar_contacto.png" value="submit">';
        $buffer.='</form>';
        $buffer.='<a href="carrito.php?carrito=recover" >Recuperar contrase�a</a>';
        echo $buffer;  
      }
    }
    else if(isset($_GET['carrito']) && $_GET['carrito']=='recover')
    {
      $buffer='<img src="resources/registro.jpg" />';
      $buffer.='<form action="carrito.php?carrito=dorecover" method="post" >';
      $buffer.='<table>';
      $buffer.='<tr><td><label class="form-text" >Nombre de Usuario</label></td><td><input type="text" name="username"/></td></tr>';
      $buffer.='</table>';
      $buffer.='<input style="position:relative;top:10px;left:120px;" type="image" src="resources/boton_enviar_contacto.png" value="submit">';
      $buffer.='</form>';
      echo $buffer;
    }
    else if(isset($_GET['carrito']) && $_GET['carrito']=='dorecover')
    {
      $query = new dbQuery('select * from client where username = :p0',array($_POST['username']));
      if($query->NumRows()!=0 && $query->NextValue())
      {
        $to      = $query->result['EMAIL'];
        $subject = 'Recuperaci\F3n de contrase\F1a - peluchesfantasia.com';
        $headers = 'From: PELUCHESFANTASIA.COM <info@peluchesfantasia.com>' . "\r\n" .
                   'Reply-To: pedido@peluchesfantasia.com' . "\r\n" .
                   'X-Mailer: PHP/' . "\r\n" .
                   'Content-type: text/html' . "\r\n";
        $message = 'Su contrase\F1a es: '.$query->result['PASSWORD'];
        $buffer='<img src="resources/barra_pedido_login.jpg" />';
        if(mail($to, $subject, $message, $headers))
        {
          $buffer.='<label class="form-text">La contrase\F1a fue enviada a su email</label>';
          $buffer.='<a href="carrito.php?carrito=login"><img src="resources/boton_volver.png" /></a>';
        }
        else
        {
          $buffer.='<label class="form-text">Ha ocurrido un error</label>';
          $buffer.='<a href="carrito.php?carrito=login"><img src="resources/boton_volver.png" /></a>';
        }
      }
      else
      {
          $buffer.='<label class="form-text">El usuario solicitado no existe</label>';
          $buffer.='<a href="carrito.php?carrito=login"><img src="resources/boton_volver.png" /></a>';
      }
      echo $buffer;
    }
    else
    {
      $buffer='<img src="resources/barra_pedido.jpg"/>';
      $buffer.=showOrderedProducts(true);
      $buffer.='<table>';
      $buffer.='<tr><td><a href="carrito.php?carrito=fill" ><img src="resources/boton_enviar_pedido.png" /></a></td>';
      $buffer.='<td><a href="carrito.php?carrito=login" ><img src="resources/ingresar.png" /></a></td></tr>';
      $buffer.='</table>';
      echo $buffer;
    }
  }
  else
  {
    echo '<p class="contact-message-success" >No hay ning\FAn producto agregado al pedido</p>';
  }
}

function sendOrder()
{
  $buffer=loadDataTable();
  $buffer.=showOrderedProducts(false);

  $to      = 'mundo.de.fantasia@hotmail.com';
  $subject = 'Pedido de Compra - peluchesfantasia.com';
  $headers = 'From: PELUCHESFANTASIA.COM <info@peluchesfantasia.com>' . "\r\n" .
'Reply-To: pedido@peluchesfantasia.com' . "\r\n" .
'X-Mailer: PHP/' . "\r\n" .
'Content-type: text/html' . "\r\n";

  if(mail($to, $subject, $buffer, $headers))
    echo '<p class="contact-message-success"> Felicitaciones! su pedido ha sido realizado con �xito. Un integrante de nuestro staff se comunicar� con usted a la brevedad. <a href="productos.php">Volver</a></p>';
  else
    echo '<p class="contact-message-success"> Error enviando pedido</p>';

  unset($_SESSION['pedido']);
  session_destroy();
}

function saveClientData()
{
  $username = $_POST['username'];
  $query = "select * from client where username='$username'";
  $clients = new dbQuery($query);
  $error='';
  if($clients->NumRows()==0)
  {
    $transaction = new dbtransaction();
    $error = $transaction->DoTransaction('insert into client (username,password,name,firmname,address,city,state,cp,telephone,fax,email,web,cativa,cuit,transport) values (:p0,:p1,:p2,:p3,:p4,:p5,:p6,:p7,:p8,:p9,:p10,:p11,:p12,:p13,:p14)',array($_POST['username'],$_POST['password'],$_POST['name'],$_POST['socialreason'],$_POST['address'],$_POST['city'],$_POST['state'],$_POST['cp'],$_POST['tel'],$_POST['fax'],$_POST['email'],$_POST['web'],$_POST['cativa'],$_POST['cuit'],$_POST['transport']));
  }
  else
  {
    $error='El usuario ya existe';
  }
  return $error;
}

function loadDataTable()
{
  $buffer='<div id="cart-table">';
  $buffer='<table style="position:relative;width:493px;left:20px;">';
  $buffer.='<tr><td><table><tr><td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Nombre y Apellido</label><br/>'.$_POST['name'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Raz\F3n Social</label><br/>'.$_POST['socialreason'].'</td></tr></table></td></tr>';
  
  $buffer.='<tr><td><table><tr><td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Domicilio</label><br/>'.$_POST['address'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Localidad</label><br/>'.$_POST['city'].'</td></tr></table></td></tr>';
  
  $buffer.='<tr><td><table><tr><td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Provicia</label><br/>'.$_POST['state'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >C.P.</label><br/>'.$_POST['cp'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Tel\E9fono</label><br/>'.$_POST['tel'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Fax</label><br/>'.$_POST['fax'].'</td></tr></table></td></tr>';
  
  $buffer.='<tr><td><table><tr><td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >E-mail</label>'.$_POST['email'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Web</label>'.$_POST['web'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Categor\EDa I.V.A.</label>'.$_POST['cativa'].'</td></tr></table></td></tr>';
  
  $buffer.='<tr><td><table><tr><td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >C.U.I.T.</label><br/>'.$_POST['cuit'].'<br/>';
  $buffer.='<label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Transporte</label><br/>'.$_POST['transport'].'</td>';
  $buffer.='<td><label style="font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;color:#752C85;" >Comentario</label><br/>'.$_POST['comment'].'</td></tr></table></td></tr>';
  $buffer.='</table>';
  $buffer.='</div>';
  return $buffer;
}
function showOrderedProducts($canteliminate)
{
  $buffer='<div style="position:relative;width:540px;font-family:helvetica,sans-serif;font-weight:bold;font-size:12px;background-color:#F7F8FA;">';
  if($canteliminate)
  {
    $buffer.='<table id="table-product" >';
  }
  else
  {
    $buffer.='<table style="font-family:helvetica,sans-serif;font-size:12px;background-color:#F7F8FA;position:relative;color:#752C85;width:494px;left:20px;border-width:1px;border-spacing:0px;border-collapse:collapse;border-style:solid;-moz-border-radius: 5px;-webkit-border-radius: 5px;" >';
  }
  $buffer.='<tr ><th   style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;" >C\F3digo</th><th   style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;"  >Categor\EDa</th><th   style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;"  >Producto</th><th   style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;"  >Cant.</th><th   style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;"  >$ Unit.</th><th   style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;" >Subtotal</th>';
  if($canteliminate)
    $buffer.='<th style="font-family:helvetica,sans-serif;font-size:12px;border:1px solid #752C85;height:25px;color:#752C85;-moz-border-radius: 5px;-webkit-border-radius: 5px;" ></th>';
  $buffer.='</tr>';
  $total = 0;
  foreach($_SESSION['pedido'] as $key => $product)
  {
    $subtotal=$product['precio']*$product['cantidad'];
    $buffer.='<tr id="ordered-product-'.$key.'" ><td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" >'.$key.'</td>';
    $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" >'.$product['categoria'].'</td>';
    $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" >'.$product['nombre'].'</td>';
    if($canteliminate)
      $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" ><input type="text" size="2" value="'.$product['cantidad'].'" onchange="javascript:recalculatePrice(\''.$key.'\')" /></td>';
    else
      $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" >'.$product['cantidad'].'</td>';
    $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" >$'.$product['precio'].'</td>';
    $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" >$'.number_format($subtotal,2).'</td>';
    if($canteliminate)
      $buffer.='<td style="border-right:1px solid #752C85;border-left:1px solid #752C85;" ><a href="javascript:clearProduct(\''.$key.'\')" ><img src="resources/ico_eliminar.jpg" /></a></td>';
    $buffer.='</tr>';
    $total+=$subtotal;
  }
  $buffer.='</table>';
  $buffer.='<p id="ordered-total" >Total $'.$total.'</p></div>';
  return $buffer;
}

function fillForm()
{
  
}

function showAllProducts()
{
  $query = "select * from product p order by p.id ASC";
  $products = new dbQuery($query);
  while($products->NextValue()) {
    $list[] = array(
      'code' => $products->result['CODE'],
      'name' => $products->result['NAME'],
      'description' => $products->result['DESCRIPTION'],
      'price' => $products->result['PRICE'],
      'image' => $products->result['IMAGE'],
      'video' => $products->result['VIDEO']
    );
  }
  echo show($list);
}
function topMenu()
{
  $result = array();
  $query = "select * from category";
  $categories = new dbQuery($query);
  $page = 0; //$_GET['page'];
  while($categories->NextValue())
  {
    $result[] = array('id'=>$categories->result['ID'],
      'name'=>$categories->result['NAME'],
      'image'=>$categories->result['IMAGE']);
  }
  
  if(is_array($result))
  {
    $columns = 2;
    $size = 7;
    $from = $size*$columns*$page;
    $to = $size*$columns*($page+1);
//     echo "$from $to";
    echo '<table><tr>';
    $count = 0;
    foreach($result as $key=>$prop)
    {
      if($count>=$from && $count<$to)
      {
	$nl = ($count%$columns)==0;
	if($nl && $count!=0)
	{
	  echo '</tr><tr>';
	}
	echo '<td>';
	echo '<a href="productos.php?products=show&search=category&id='.$prop['id'].'" ><img src="resources/category/'.$prop['image'].'" /></a>';
	echo '<br>';
	$products = new dbQuery("select * from subcategory where idcategory='{$prop['id']}' order by sequence");
	$subindex = 0;
	$rows = $products->NumRows();
	echo '<div style="text-align:justify;position:relative;left:5px;width:250px" >';	
	while($products->NextValue())
	{
	  $rows--;
	  echo '<a class="menu-top-subcategory" href="productos.php?products=show&search=subcategory&id='.$products->result['ID'].'" >';
	  echo "{$products->result['NAME']}";
	  echo '</a>';
	  if($rows!=0)
	    echo '<span style="position:relative;left:10px;color:#F29400"> - </span>';
	}
	echo '</div>';
	echo "</td>";
      }
      $count++;
    }
    echo "</tr></table>";
  }
}
function sideMenu()
{
  $result = array();
  $query = "select * from category";
  $categories = new dbQuery($query);
  $page = 0; //$_GET['page'];
  while($categories->NextValue())
  {
    $result[] = array('id'=>$categories->result['ID'],'name'=>$categories->result['NAME']);
  }
  $buffer='<table id="side-menu" >';
  if(is_array($result))
  {
    $columns = 1;
    $size = -1;
    $from = $size*$columns*$page;
    $to = $size*$columns*($page+1);
    $count = 0;
    foreach($result as $key=>$prop)
    {
      $buffer.='<tr onclick="javascript:updateSubmenu('.$count.');"><td>';
//       echo '<td  style="cursor:pointer;text-align:left;font-family:helvetica,sans-serif;font-weight:bold;font-size:14px;color:#F29400;text-transform:capitalize">';
      $buffer.='<p class="category-left-menu" >'.htmlentities($prop['name']).'</p>';
      
      $buffer.='<table id="side-submenu-'.$count.'" style="display:none">';
      $products = new dbQuery("select * from subcategory where idcategory='{$prop['id']}' order by sequence");
      while($products->NextValue())
      {
// var url = 'productos.php?products=show&search=subcategory&id='+;
// 	$buffer.='<tr><td style="color:#F29400;" >&bull;<a class="subcategory-left-menu" href="javascript:submenuSearch(\''.$products->result['ID'].'\');" >'.$products->result['NAME'].'</a></td></tr>';
	$buffer.='<tr><td style="color:#F29400;" >&bull;<a class="subcategory-left-menu" href="productos.php?products=show&search=subcategory&id='.$products->result['ID'].'" >'.$products->result['NAME'].'</a></td></tr>';
      }
      $buffer.='</table>';
      $buffer.='</td></tr>';
      $count++;
    }
    $buffer.='</table>';
    echo $buffer;
  }
}

function home()
{
  echo '<img src="resources/filosofia.jpg"/>
	<img src="resources/destacados.png" />
	<div id="slider">
	  <div id="slider-img">';
      $products = new dbQuery('select * from product where exclusive=1');

      while($products->NextValue())
      {
	echo '<img src="resources/resized/resized-elemento-'.$products->result['IMAGE'].'" />';
      }
  echo '</div>
	</div>';
}
  
function home2()
{
  echo '<img src="resources/filosofia.jpg"/>
	<img src="resources/destacados.png" />
	<div id="slider">
	  <div id="slider-img">';
      $products = new dbQuery('select * from product where exclusive=1');
	var_dump($products);
      while($products->NextValue())
      {
	echo '<img src="resources/resized/resized-elemento-'.$products->result['IMAGE'].'" />';
      }
  echo '</div>
	</div>';
}
function contact()
{
  if(isset($_GET['contact']) && $_GET['contact']=='send')
  {
    if($_POST['mail']=="" || $_POST['name']=="" || $_POST['empresa']=="" || $_POST['comment']=="" )
    {
      echo '<p style="font-family:helvetica,sans serif;color:#D4251A" >Por favor complete todos los campos solicitados.<a href="contacto.php?contact=show" >Volvera</a></p>';
    }
    else
    {
      $email = $_POST['mail'];
//       $buffer='<html><head/><body>';
//       $buffer.='<label style="font-family:helvetica,sans serif;font-weight:bold;font-size:12px;color:#D4251A;" >Nombre</label><br/>'.$_POST['name'].'<br/><br/>';
//       $buffer.='<label style="font-family:helvetica,sans serif;font-weight:bold;font-size:12px;color:#D4251A;" >Empresa</label><br/>'.$_POST['empresa'].'<br/><br/>';
//       $buffer.='<label style="font-family:helvetica,sans serif;font-weight:bold;font-size:12px;color:#D4251A;" >Comentario</label><br/>'.$_POST['comment'].'</textarea><br/><br/>';
//       $buffer.='</body></html>';
	$buffer="Nombre: ".$_POST['name']."\n";
	$buffer.="Empresa: ".$_POST['empresa']."\n";
	$buffer.="Email: ".$_POST['mail']."\n";
	$buffer.="Comentario: ".$_POST['comment']."\n";
      $to      = 'mundo.de.fantasia@peluchesfantasia.com,info@peluchesfantasia.com';
      $subject = 'Comentario - peluchesfantasia.com';
      $headers .= 'To: extobias@gmail.com'."\r\n";
      $headers .= 'From: info@peluchesfantasia.com' . "\r\n";
      $headers .= 'Reply-To: info@peluchesfantasia.com' . "\r\n";
      $headers .= 'X-Mailer: PHP' . "\r\n";

      if(mail($to,$subject,$buffer,$header))
      {
	echo '<p style="font-family:helvetica,sans serif;color:#D4251A" >Su mensaje ha sido enviado con \E9xito, le responderemos a la brevedad.<a href="contacto.php?contact=show" >Volver</a></p>';
      }
      else
      {
	echo '<p style="font-family:helvetica,sans serif;color:#D4251A" >Ha ocurrido un error. Por favor intente mas tarde<a href="contacto.php?contact=show" >Volver</a></p>';
      }
    }
  }
  else //if($_GET['contact']=='show')
  {
    //$buffer='<img style="position:relative;top:30px" src="resources/ubicacion.png" />';
	$buffer='<img style="position:relative;top:30px" src="resources/telefono-y-correo.jpg" />';
    //$buffer.='<iframe width="350" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="20" src="http://maps.google.com.ar/maps?f=d&amp;source=s_d&amp;saddr=Pasteur+209&amp;daddr=&amp;hl=es&amp;geocode=&amp;mra=ls&amp;sll=-34.597819,-58.417761&amp;sspn=0.009026,0.01929&amp;ie=UTF8&amp;ll=-34.607199,-58.399219&amp;spn=0.006295,0.007649&amp;output=embed"></iframe><br />';
    //$buffer.='<img style="position:relative;top:50px" src="resources/imagen_local.jpg" />';
    $buffer.='<div style="position:absolute;left:400px;top:30px;" >';
    $buffer.='<form action="contacto.php?contact=send" method="post" >';
    $buffer.='<label class="contact-info-title" >NOMBRE Y APELLIDO</label><br/><input class="form-input"  type="text" name="name" /><br/><br/>';
    $buffer.='<label class="contact-info-title" >EMAIL</label><br/><input class="form-input" type="text" name="mail" /><br/><br/>';
    $buffer.='<label class="contact-info-title" >EMPRESA</label><br/><input class="form-input" type="text" name="empresa" /><br/><br/>';
    $buffer.='<label class="contact-info-title" >COMENTARIO</label><br/><textarea class="form-input-text" name="comment" ></textarea><br/><br/>';
    $buffer.='<input style="position:relative;top:10px;left:120px;" type="image" src="resources/boton_enviar_contacto.png" value="submit">';
    $buffer.='</form>';
    $buffer.='<div style="position:relative;top:30px;">';
    $buffer.='<p class="contact-info-title" >CORREO ELECTR\D3NICO<br/><span class="contact-info">info@peluchesfantasia.com<br><img style="width:24px;height:24px" src="resources/msn.jpg" >mundo.de.fantasia@hotmail.com<br/><br/></span>TEL\C9FONO<br/><span class="contact-info" >15-5346-2238</span></p>';
    $buffer.='</div></div>';
    echo $buffer;
  }
}

function barInfo()
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

?>

