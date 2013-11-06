<?
include('../db.class.php');

// Index functions

function searchMenu()
{

}

// Content functions

function showTable()
{
//   echo 'GET';
//   echo '<pre>';
//   print_r($_GET);
//   echo '</pre>';
//   echo 'POST';
//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
//   echo 'FILES';
//   echo '<pre>';
//   print_r($_FILES);
//   echo '</pre>';  

  if(isset($_GET['show']))
  {
    
    $type = $_GET['show'];
    if($type=="user")
    {
      echo showUsers();
    }
    else if($type=="subcategory")
    {
      echo showCategory();
    }
    else if($type=="product")
    {
      echo selectCategory(true);
    }
  }
  else if(isset($_GET['search']))
  {
  }
  else if(isset($_GET['edit']))
  {
    $type = $_GET['edit'];
    if($type=="user")
    {
      echo editUserForm();
    }
    else if($type=="subcategory")
    {
      echo editSubcategoryForm();
    }
    else if($type=="product")
    {
      echo editProductForm();
    }      
  }
  else if(isset($_GET['add']))
  {
    $type = $_GET['add'];
    if($type=="user")
    {
      echo addUserForm();
    }
    else if($type=="subcategory")
    {
      echo addSubcategoryForm();
    }
    else if($type=="product")
    {
      echo addProductForm();
    }    
  }
  else if(isset($_GET['remove']))
  {
    $type = $_GET['remove'];
    if($type=="user")
    {
      echo removeUser();
    }
    else if($type=="subcategory")
    {
      echo removeSubcategory();
    }
    else if($type=="product")
    {
      echo removeProduct();
    }        
  }

  // POSTing events
  if(isset($_POST['add']))
  {
    $type = $_POST['add'];
    if($type=="user")
    {
      if(isset($_POST['accept']))
	echo addUser();
      else
	echo showUsers();      
    }
    else if($type=="subcategory")
    {
      if(isset($_POST['accept']))
	echo addSubcategory();
      else
	echo showCategory();
    }
    else if($type=="product")
    {
      if(isset($_POST['accept']))
	echo addProduct();
      else
      {
	echo selectCategory(true);
      }
    }        
  }
  else if(isset($_POST['edit']))
  {
    $type = $_POST['edit'];
    if($type=="user")
    {
      if(isset($_POST['accept']))
	echo editUser();
      else
	echo showUsers();
    }
    else if($type=="subcategory")
    {
      if(isset($_POST['accept']))
	echo editSubcategory();
      else
	echo showCategory();
    }
    else if($type=="product")
    {
      if(isset($_POST['accept']))
	echo editProduct();
      else
      {
	echo selectCategory(true);
      }
    }            
    
  }
}

function showUsers()
{
    $query = 'select * from users';
    $result = new dbQuery($query);
    while($result->NextValue()) {
      $list[] = array('id' => $result->result['ID'],
		      'username' => $result->result['USERNAME'],
		      'password' => $result->result['PASSWORD']);
    }
    
    $buffer='<table id="user-table" border="1" ><tr>';
    $buffer.='<th class="content-table-head" >Id</th><th class="content-table-head" >Usuario</th><th class="content-table-head" >Contraseña</th>';
    $buffer.='</tr>';
    if(is_array($list))
    {
      foreach($list as $key=>$val)
      {
	$buffer.='<tr onclick="javascript:highlight(this);" >';
	$buffer.='<td class="content-table-cell" >'.$val['id'].'</td>';
	$buffer.='<td class="content-table-cell" >'.$val['username'].'</td>';
	$buffer.='<td class="content-table-cell" >'.$val['password'].'</td>';
	$buffer.='</tr>';
      }
    }
    $buffer.='</table>';
    return $buffer;
}

function showCategory()
{
  $buffer.='Categoria: <select id="category-select" >';
  $query = "select * from category";
  $categories = new dbQuery($query);
  while($categories->NextValue())
  {
    $result[$categories->result['ID']] = $categories->result['NAME'];
  }
  foreach($result as $key=>$val)
  {
    $buffer.='<option value="'.$key.'" onclick="javascript:showSubcategory()" >'.htmlentities($val).'</option>';
  }
  $buffer.='</select>';
  $buffer.='<div id="subcategory-container" />';
  return $buffer;
}

function showSubcategory($category)
{
    $query = "select * from subcategory where idcategory='$category' order by sequence";
    $result = new dbQuery($query);
    while($result->NextValue()) {
	$list[] = array('id' => $result->result['ID'],
		      'name' => $result->result['NAME'],
		      'description' => $result->result['DESCRIPTION'],
		      'category' => $result->result['IDCATEGORY'],
		      'image' => $result->result['IMAGE']);
    }
    $buffer.='<ul id="subcategory-list" >';
    if(is_array($list))
    {
      foreach($list as $key=>$val)
      {
	$buffer.='<li class="content-list" id="subcategory_'.$val['id'].'" onclick="javascript:highlightList(this);">'.$val['name'].'</li>';
      }
    }
    $buffer.='</ul>';
    return $buffer;
}

function showProducts($subcategory)
{
    if($subcategory==-1)
      $query = "select * from product";	
    else
      $query = "select * from product where subcategory='$subcategory'";
    $result = new dbQuery($query);
    while($result->NextValue()) {
	$list[] = array('id' => $result->result['ID'],
		  'code' => $result->result['CODE'],
		  'name' => $result->result['NAME'],
		  'description' => $result->result['DESCRIPTION'],
		  'price' => $result->result['PRICE'],
		  'image' => $result->result['IMAGE'],
		  'video' => $result->result['VIDEO'],
		  'subcategory' => $result->result['SUBCATEGORY'],
		  'exclusive' => $result->result['EXCLUSIVE']);
    }
   
    $query = "select * from subcategory";
    $subcategories = new dbQuery($query);
    while($subcategories->NextValue())
    {
      $resultsubcat[$subcategories->result['ID']] = array('name'=>$subcategories->result['NAME'],'category'=>$subcategories->result['IDCATEGORY']);
    }
    
    $buffer='<table id="product-table"><thead><tr>';
    $buffer.='<th class="content-table-head" >Id</th><th class="content-table-head" >Código</th><th class="content-table-head" >Nombre</th><th class="content-table-head" >Descripción</th><th class="content-table-head" >Precio</th><th class="content-table-head" >Imágen</th><th class="content-table-head" >Subcategoria</th><th class="content-table-head" >Destacado</th><th class="content-table-head" >Video</th>';    
    $buffer.='</tr></thead><tbody style="overflow-y:auto;overflow-x:hidden;width:100%;height:500px" >';
    
    if(is_array($list))
    {
      foreach($list as $key=>$val)
      {
	$buffer.='<tr onclick="javascript:highlight(this);" >';
	$buffer.='<td class="content-table-cell" >'.$val['id'].'</td>';
	$buffer.='<td class="content-table-cell" >'.$val['code'].'</td>';
	$buffer.='<td class="content-table-cell" >'.htmlentities($val['name']).'</td>';
	$buffer.='<td class="content-table-cell" >'.htmlentities($val['description']).'</td>';
	$buffer.='<td class="content-table-cell" >'.$val['price'].'</td>';
	$buffer.='<td class="content-table-cell" ><img style="width:64px;height:64;" src="../resources/thumbnail/elemento-'.$val['image'].'" /></td>';
	$subcategory = $val['subcategory'];
	$buffer.='<td class="content-table-cell" >'.$resultsubcat[$subcategory]['name'].'</td>';
	$buffer.='<td class="content-table-cell" >'.$val['exclusive'].'</td>';
	$buffer.='<td class="content-table-cell" >'.$val['video'].'</td>';
	$buffer.='</tr>';
      }
    }
    $buffer.='</tbody></table>';
    return $buffer;
}

function addUserForm()
{
  $buffer='<form action="content.php" method="post" >';
  $buffer.='Nombre: <input type="text" name="username" /><br/>';
  $buffer.='Password: <input type="text" name="password" /><br/>';
  $buffer.='<input type="hidden" name="add" value="user" />';
  $buffer.='<input type="submit" name="accept" value="Agregar" />';
  $buffer.='<input type="submit" name="cancel" value="Cancelar" />';
  $buffer.='</form>';
  return $buffer;
}

function addSubcategoryForm()
{
  $buffer='<form action="content.php" method="post" enctype="multipart/form-data" >';
  $buffer.='Nombre: <input type="text" name="name" /><br/>';
  $buffer.='Categoria: <select name="category" >';
  $buffer.='<option value="1" >Pantuflas</option>';
  $buffer.='<option value="2" >Art. de bebe</option>';
  $buffer.='<option value="3" >Muñecas</option>';
  $buffer.='<option value="4" >Musicales</option>';
  $buffer.='<option value="5" >Peluches chicos</option>';
  $buffer.='<option value="6" >Peluches medianos</option>';
  $buffer.='<option value="7" >Peluches grandes</option>';
  $buffer.='<option value="8" >Varios</option>';
  $buffer.='</select><br/>';
  $buffer.='<input type="hidden" name="add" value="subcategory" />';
  $buffer.='<input type="submit" name="accept" value="Agregar" />';
  $buffer.='<input type="submit" name="cancel" value="Cancelar" />';
  $buffer.='</form>';
  return $buffer;
}

function addProductForm()
{
  $buffer='<form name="product-form" action="content.php" method="post" enctype="multipart/form-data" >';
  $buffer.=selectCategory(false);
  $buffer.='Código: <input type="text" name="code" /><br/>';
  $buffer.='Nombre: <input type="text" name="name" /><br/>';
  $buffer.='Descripción: <input type="text" name="description" /><br/>';
  $buffer.='Precio: <input type="text" name="price" /><br/>';
  $buffer.='Imágen: <input type="file" name="file" /><br/>';
  $buffer.='Video: <input type="text" name="video" /><br/>';
  $buffer.='Destacado <input type="checkbox" name="prominent" /><br/>';
  $buffer.='<input type="hidden" name="add" value="product" />';
  $buffer.='<input type="submit" name="accept" value="Agregar" />';
  $buffer.='<input type="submit" name="cancel" value="Cancelar" />';
  $buffer.='</form>';
  return $buffer;
}

function editUserForm()
{
  $buffer='<form action="content.php" method="post" >';
  $buffer.='<input type="hidden" name="id" value="'.$_GET['id'].'" />';
  $buffer.='Nombre: <input type="text" name="username" value="'.$_GET['username'].'" /><br/>';
  $buffer.='Password: <input type="text" name="password" value="'.$_GET['password'].'" /><br/>';
  $buffer.='<input type="hidden" name="edit" value="user" />';
  $buffer.='<input type="submit" name="accept" value="Actualizar" />';
  $buffer.='<input type="submit" name="cancel" value="Cancelar" />';
  $buffer.='</form>';
  return $buffer;
}

function editSubcategoryForm()
{
  $buffer='<form action="content.php" method="post" enctype="multipart/form-data" >';
  $buffer.='<input type="hidden" name="id" value="'.$_GET['id'].'" />';
  $buffer.='Nombre: <input type="text" name="name" value="'.$_GET['name'].'" /><br/>';
//   $buffer.='Descripción: <input type="text" name="description" value="'.$_GET['description'].'" /><br/>';
  $buffer.='Categoria: <select name="category" >';
  $category = $_GET['category'];
  $buffer.='<option '.(($category==1)?'selected':'').' value="1" >Pantuflas</option>';
  $buffer.='<option '.(($category==2)?'selected':'').' value="2" >Articulos de bebe</option>';
  $buffer.='<option '.(($category==3)?'selected':'').' value="3" >Muñecas</option>';
  $buffer.='<option '.(($category==4)?'selected':'').' value="4" >Musicales</option>';
  $buffer.='<option '.(($category==5)?'selected':'').' value="5" >Peluches chicos</option>';
  $buffer.='<option '.(($category==6)?'selected':'').' value="6" >Peluches medianos</option>';
  $buffer.='<option '.(($category==7)?'selected':'').' value="7" >Peluches grandes</option>';
  $buffer.='<option '.(($category==8)?'selected':'').' value="8" >Varios</option>';
  $buffer.='</select><br/>';
//   $buffer.='Imágen: <input type="file" name="file" /><br/>';
  $buffer.='<input type="hidden" name="edit" value="subcategory" />';
  $buffer.='<input type="submit" name="accept" value="Actualizar" />';
  $buffer.='<input type="submit" name="cancel" value="Cancelar" />';
  $buffer.='</form>';
  return $buffer;
}

function editProductForm()
{
  $buffer='<form action="content.php" method="post" enctype="multipart/form-data" >';
  $buffer.='<input type="hidden" name="id" value="'.$_GET['id'].'" />';
  $buffer.='Código: <input type="text" name="code" value="'.$_GET['code'].'" /><br/>';
  $buffer.='Nombre: <input type="text" name="name" value="'.$_GET['name'].'" /><br/>';
  $buffer.='Descripción: <input type="text" name="description" value="'.$_GET['description'].'" /><br/>';
  $buffer.='Precio: <input type="text" name="price" value="'.$_GET['price'].'" /><br/>';
  $buffer.=selectCategory(false);
  $buffer.='Imágen: <input type="file" name="file" /><br/>';
  $buffer.='Video: <input type="text" name="video" value="'.$_GET['video'].'" /><br/>';
  $buffer.='Destacado <input type="checkbox" name="prominent" value="'.$_GET['exclusive'].'" '.(($_GET['exclusive']==1)?'checked':'').' /><br/>';
  $buffer.='<input type="hidden" name="edit" value="product" />';
  $buffer.='<input type="submit" name="accept" value="Actualizar" />';
  $buffer.='<input type="submit" name="cancel" value="Cancelar" />';
  $buffer.='</form>';
  return $buffer;
}

function addUser()
{
  if(isset($_POST['username']) && isset($_POST['password']) && $_POST['username']!="" && $_POST['password']!="")
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('insert into users (username,password) values (:p0,:p1)',array($_POST['username'],$_POST['password']));
    echo showUsers();
  }
  else
    echo 'Los datos suministrados no son suficientes para dar de alta un usuario';  
}

function addSubcategory()
{
  if(isset($_POST['name']) && isset($_POST['category']) && $_POST['name']!="" && $_POST['category']!="")
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('insert into subcategory (name,description,idcategory,image) values (:p0,:p1,:p2,:p3)',array($_POST['name'],'',$_POST['category'],''));  
    echo showCategory();
  }
  else
    echo 'Los datos suministrados no son suficientes para dar de alta una subcategoría';  
}

function addProduct()
{
 if(isset($_POST['code']) && isset($_POST['name']) && isset($_POST['description']) && $_POST['code']!="" && $_POST['name']!="" && $_POST['description']!="")
  {
    if(is_uploaded_file($_FILES['file']['tmp_name']))
    {
      srand((double)microtime()*1000000);
      $random =  rand(0,8000);
      srand((double)microtime()*1000000);
      $random .=  rand(0,8000);

      $path=str_replace('admin','',dirname(__FILE__)).'/resources/thumbnail/elemento-';
      $imgname=$random.'.jpg';
      if(!move_uploaded_file($_FILES['file']['tmp_name'], $path.$imgname))
      {
	echo 'No se puede guardar la imagen';
	return;
      }
    }
    if(isset($_POST['prominent']))
    {
      $exclusive=1;
      createImageGallery($imgname,$_POST['name'],$_POST['description'],$_POST['price']);
    }
    else
    {
      $exclusive=0;
    }
    $transaction = new dbtransaction();
    $transaction->DoTransaction('insert into product (code,name,description,price,image,video,subcategory,exclusive) values (:p0,:p1,:p2,:p3,:p4,:p5,:p6,:p7)',array($_POST['code'],$_POST['name'],$_POST['description'],$_POST['price'],$imgname,$_POST['video'],$_POST['subcategory'],$exclusive));  
    echo selectCategory(true);
  }  
  else
    echo 'Los datos suministrados no son suficientes para dar de alta un producto';
}

function editUser()
{
  if(isset($_POST['id']) && isset($_POST['username']) && isset($_POST['password']) && $_POST['id']!="" && $_POST['username']!="" && $_POST['password']!="")
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('update users set username = :p0, password = :p1 where id = :p2',array($_POST['username'],$_POST['password'],$_POST['id']));
    echo showUsers();
  }
  else
    echo 'Los datos suministrados no son suficientes para modificar un usuario';
}

function editSubcategory()
{
  if(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['category']) && $_POST['id']!="" && $_POST['name']!="" && $_POST['category']!="")
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('update subcategory set name = :p0, idcategory = :p1 where id = :p2',array($_POST['name'],$_POST['category'],$_POST['id']));  
    echo showCategory();
  }
  else
  {
    echo 'Los datos suministrados no son suficientes para modificar una subcategoria';  
  }
}

function editProduct()
{
  if(isset($_POST['id']) && isset($_POST['code']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['subcategory']) && isset($_POST['price']) && 
      $_POST['id']!="" && $_POST['code']!="" && $_POST['name']!="" && $_POST['description']!="" && $_POST['subcategory']!="" && $_POST['price']!="")
  {
    if(isset($_POST['prominent']))
      $exclusive=1;
    else
      $exclusive=0;
    
    if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']))
    {
      srand((double)microtime()*1000000);
      $random =  rand(0,8000);
      srand((double)microtime()*1000000);
      $random .=  rand(0,8000);
//       echo $random;
      $path=str_replace('admin','',dirname(__FILE__)).'/resources/thumbnail/elemento-';
      $imgname=$random.'.jpg';
      if(!move_uploaded_file($_FILES['file']['tmp_name'], $path.$imgname))
      {
	echo 'cannot move tmp file';
	return;
      }
      
      if($exclusive)
      {
	createImageGallery($imgname,$_POST['name'],$_POST['description'],$_POST['price']);
      }      
      
      $transaction = new dbtransaction();
      $transaction->DoTransaction('update product set code = :p0, name = :p1, description = :p2, price = :p3, image = :p4, video = :p5, subcategory = :p6, exclusive = :p7  where id = :p8',array($_POST['code'],$_POST['name'],$_POST['description'],$_POST['price'],$imgname,$_POST['video'],$_POST['subcategory'],$exclusive,$_POST['id']));  
    }    
    else
    {
      if($exclusive)
      {
	$result = new dbQuery('select image from product where id ='.$_POST['id']);
	if($result->NextValue())
	{
	  $imgname = $result->result['IMAGE'];
	}
	createImageGallery($imgname,$_POST['name'],$_POST['description'],$_POST['price']);
      }      
      
      $transaction = new dbtransaction();
      $transaction->DoTransaction('update product set code = :p0, name = :p1, description = :p2, price = :p3, video = :p4, subcategory = :p5, exclusive = :p6  where id = :p7',array($_POST['code'],$_POST['name'],$_POST['description'],$_POST['price'],$_POST['video'],$_POST['subcategory'],$exclusive,$_POST['id']));
    }
    echo selectCategory(true);
  }
  else
  {
    echo 'Los datos suministrados no son suficientes para modificar un producto';
  }
}

function removeUser()
{
  if(isset($_GET['id']))
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('delete from users where id = :p0',array($_GET['id']));
    echo showUsers();
  }
}

function removeSubcategory()
{
  if(isset($_GET['id']))
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('delete from subcategory where id = :p0',array($_GET['id']));
    echo showCategory();
  }
}

function removeProduct()
{
  if(isset($_GET['id']))
  {
    $transaction = new dbtransaction();
    $transaction->DoTransaction('delete from product where id = :p0',array($_GET['id']));
    echo selectCategory(true);
  }  
}

function selectCategory($showproducts)
{
  $buffer.='Categoria: <select name="category" id="category-select" onclick="selectSubcategory('.(($showproducts)?'1':'0').')">';
  $query = "select * from category";
  $categories = new dbQuery($query);
  while($categories->NextValue())
  {
    $result[$categories->result['ID']] = $categories->result['NAME'];
  }
  foreach($result as $key=>$val)
  {
    $buffer.='<option value="'.$key.'" '.(($key==$_GET['idcategory'])?'selected':'').'  >'.htmlentities($val).'</option>';
  }
  
  if($showproducts)
    $buffer.='<option value="-1">TODOS</option>';
  $buffer.='</select>';
  if($showproducts)
  {
    $buffer.='Subcategoria: <select id="subcategory-select" onclick="showProducts()">';
  }
  else
  {
    $buffer.='Subcategoria: <select id="subcategory-select" name="subcategory" onclick="showProducts()">';
  }
  if(isset($_GET['idcategory']) && $_GET['idcategory']!=0)
    $category=$_GET['idcategory'];
  else
    $category=1;
  $buffer.=selectSubcategory($category,$showproducts);
  $buffer.='</select><br/>';  
  $buffer.='<div id="subcategory-container" />';
  return $buffer;
}

function selectSubcategory($category,$showproducts)
{
  $query = "select * from subcategory where idcategory='$category'";
  $subcategories = new dbQuery($query);
  while($subcategories->NextValue())
  {
    $result[] = array('id'=>$subcategories->result['ID'],'name'=>$subcategories->result['NAME']);
  }
  if(isset($result))
  {
    foreach($result as $key=>$prop)
    {
      if($showproducts)
      {
	$buffer.='<option value="'.$prop['id'].'" '.(($prop['id']==$_GET['idsubcat'])?'selected':'').'  >'.htmlentities($prop['name']).'</option>';
      }
      else
      {
	$buffer.='<option value="'.$prop['id'].'" '.(($prop['id']==$_GET['idsubcat'])?'selected':'').' >'.htmlentities($prop['name']).'</option>';
      }
    }
  }
  else
  {
    $buffer.="Vacio ".$query;
  }
  return $buffer;
}

function saveSubcategoryOrder()
{
  $cont=1;
  foreach($_REQUEST['subcategory'] as $subcat)
  {
   $transaction = new dbtransaction();
   $transaction->DoTransaction('update subcategory set sequence = :p1 where id = :p0',array($subcat,$cont));
   $cont++;
  }
  $buffer.='Guardado';
  return $buffer;
}

function createImageGallery($imgname,$oname,$odescription,$oprice)
{
  $absolutepath=str_replace('admin','',dirname(__FILE__));
  $imgabsname=$absolutepath.'/resources/thumbnail/elemento-'.$imgname;
  $resizename=$absolutepath.'/resources/resized/resized-elemento-'.$imgname;
  
  
  $imagen = imagecreatefromjpeg($imgabsname);
  $imagendesc = imagecreatefrompng($absolutepath.'/resources/fondo_texto_galeria.png');
  $banner = imagecreatetruecolor(546,250);
  
  $namelist = preg_split('/ /',$oname);
  imagecopy($banner, $imagendesc, 246, 0, 0, 0, 300, 250);
  $offset = 55;
  foreach($namelist as $name)
  {
    imagefttext($banner, 15, 0, 290, $offset, 0x07A6E8, './helvetica.ttf',strtoupper($name));
    $offset+=20;
  }
  $desclist = preg_split('/ /',$odescription);
  if(in_array("cm",$desclist) || in_array("CM",$desclist))
  {
    if(count($desclist)==2)
    {
      imagefttext($banner, 13, 0, 300, 200, 0xFFFFFF, './helvetica.ttf',$_POST['description']);
    }
    else
    {
      
      foreach($desclist as $desc)
      {
	if($desc=="cm" || $desc=="CM")
	{
	  imagefttext($banner, 13, 0, 300, 200, 0xFFFFFF, './helvetica.ttf',$last.' '.$desc);
	  break;
	}
	$last = $desc;
      }
    }
  }
  imagefttext($banner, 13, 0, 426, 200, 0xFFFFFF, './helvetica.ttf','$'.$oprice);
  list($ancho, $alto) = getimagesize($imgabsname);
  if(imagecopyresampled($banner, $imagen, 0, 0, 0, 0, 246, 250, $ancho, $alto))
  {
    if (!imagepng($banner, $resizename))      
	echo 'No se pudo generar la imagen para la galeria';
  }
}
?>
