<?
include('../db.class.php');
if(isset($_POST['Ingresar']))
{
    $query = new dbQuery('select * from users where username = :p0 AND password = :p1',array($_POST['usuario'],$_POST['clave']));
    if($query->NextValue())
    {
        session_start();
        session_register('Logueado');
        $_SESSION['Logueado'] = true;
        $_SESSION['idUsuario'] = $query->result['ID'];
        header('location:index.php');
    }
    else
      echo 'Usuario o contraseña invalido';
}

?>
<html>
  <head>
  </head>
  <body>
    <h3>Bienvenido al administrador de contenido de Peluches Fantasia</h3>
    <form action="login.php" method="post" >
      <table>
	<tr><td><label>Usuario:</label></td><td><input type="text" name="usuario" value="<?php echo(isset($_POST['usuario'])?$_POST['usuario']:'')?>"/></td></tr>
	<tr><td><label>Clave:</label></td><td><input type="password" name="clave" value="<?php echo(isset($_POST['clave'])?$_POST['clave']:'')?>"/></td></tr>
	<tr><td colspan=2 align=center><input type="submit" name="Ingresar" value="Ingresar"/></td></tr>
      </table>      
    </form>
  </body>
</html>