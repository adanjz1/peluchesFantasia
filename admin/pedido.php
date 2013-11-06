<?
include('funciones.php');

//   echo 'POST';
//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
  if(isset($_POST['request']))
  {
    $type = $_POST['request'];
    if($type=='product')
    {
      if(isset($_POST['category']))
      {
	echo selectSubcategory($_POST['category'],$_POST['show']);
      }
      else if(isset($_POST['subcategory']))
      {
	echo showProducts($_POST['subcategory']);
      }
    }
    else if($type=='subcategory')
    {
      echo showSubcategory($_POST['category']);
    }
    else if($type=='neworder')
    {
      echo saveSubcategoryOrder();
    }
  }
?>