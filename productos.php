<?php include('funciones.php') ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es-ES" >
  <head>
    <title>.:: Mundo de Fantas&iacute;a ::. Peluches</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-Equiv="Cache-Control" Content="no-cache">
    <meta http-Equiv="Pragma" Content="no-cache">
    <meta http-Equiv="Expires" Content="0">
	
    <meta name="keywords" content="peluches, peluches fantasia, mundo fantasia, todo peluches, peluche, peluches todos los tamaÃ±os, peluchs, pelches" />
    <meta name="description" content="Gran variedad de PELUCHES de venta mayorista y minorista en Argentina" />
    <meta name="robot" content="all,index,follow">
    <meta name="revisit" content="7 days">
    
    <link rel="shortcut icon" href="resources/loguito.ico">
    <link rel="stylesheet" href="styles.css" type="text/css"/>
    <script type="text/javascript" src="helper.js" ></script>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"></script>  
   <link rel="stylesheet" href="mediaboxAdvBlack21.css" type="text/css" media="screen" />    
    <script src="mootools-1.2.4-core.js" type="text/javascript"></script>
    <script src="mediaboxAdv-1.3.1.js" type="text/javascript"></script>
    
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-9823927-2']);
      _gaq.push(['_trackPageview']);
      (function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>    
  </head>
  <body onload="javascript:resizemf();">
    <div id="face-button" >
      <iframe src="http://www.facebook.com/plugins/like.php?href=www.peluchesfantasia.com&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=25" scrolling="no" frameborder="0" style="border:none; width:450px; height:25px;" allowTransparency="true"></iframe>
    </div>
    <div class="header-back">
	    <img src="resources/fondologosombra.jpg" width="1260" height="240" alt="Bienvenido a Peluches Fantasia" border="0" usemap="#facemap" />
      <map name="facemap">
        <area shape="rect" coords="895,175,1060,185" href="http://es-la.facebook.com/people/Mundo-de-Fantasia/100001472088101" target="_blank" alt="Facebook" />
      </map>
      <div id="header-front" >
        <div class="menu-button" >
          <a href="index.php" ><span style="visibility:hidden;display:none;" >HOME</span><div id="menu-button-home" ></div></a> 
        </div>
        <div class="menu-button" >
          <a href="productos.php" ><span style="visibility:hidden;display:none;" >PRODUCTOS</span><div id="menu-button-productos" style="background-image:url(resources/buttonbox/productos-color.png);" ></div></a>
        </div>
        <div class="menu-button" >
          <a href="carrito.php" ><span style="visibility:hidden;display:none;" >VER CARRITO</span><div id="menu-button-carrito"></div></a> 
        </div>
        <div class="menu-button" >
          <a href="contacto.php" ><span style="visibility:hidden;display:none;" >CONTACTO</span><div id="menu-button-contacto"></div></a> 
        </div>
      </div>
    </div>
    <div id="workarea" >
      <div id="menu-left" >
        <div id="searcher" >
            <input id="searcher-input" type="text" name="input" value="" onkeyup="auto_complete()" />
            <input id="searcher-buttom" type="submit" name="search" value="OK" onclick="javascript:searchProduct()"/>
            <div id="searcher-popup" tabIndex="0" ></div>
        </div>
        <div id="menu-container" >
	  <?php sideMenu(); ?>
        </div>
        <div class="swfleft">
          <object id="como_comprar" data="resources/como_comprar2.swf" type="application/x-shockwave-flash" width="160" height="225" >
            <param name="movie" value="resources/como_comprar2.swf" />
            <param name="quality" value="high" />
            <param name="wmode" value="transparent" />
            <img src="flash.png" alt="Compre sus productos a traves de nuestro carrito online" width="230" height="100" />
            <embed src="resources/como_comprar2.swf" wmode="transparent" width="160" height="225" swliveconnect="true" name="como_comprar"></embed>
          </object>
        </div> 
        <p style="position:relative;top:60px;left:45px;width:160px;">
          <span style="font-family:helvetica,sans-serif;font-weight:bold;font-size:15px;color:#F29400;" >Sitio de venta on line</span><br/>
          <span style="font-family:helvetica,sans-serif;font-weight:bold;font-size:11px;color:#F29400">Ventas por mayor y menor<br/><br/>Teléfono 15-5346-2238<br/>info@peluchesfantasia.com</span>
          <span style="font-family:helvetica,sans-serif;font-weight:bold;font-size:11px;color:#7BB31D;"><br/><br/>Los precios expresados son en pesos Argentinos y no incluyen IVA</span>
        </p>
        <a id="esmokin" href="http://www.esmokinhd.com.ar" target="_blank" ><img style="position:relative;top:170px;left:45px;border:none" src="resources/logoesmokin.png" /></a>
      </div>    
      <div id="products" ><?php isset($_GET['search'])?showSearchedProducts():topMenu(); ?></div>
    </div>
    <?php if(count($_SESSION['pedido']))
	{ echo '<div id="static-bar" name="carbar" >'; echo barInfo(); echo '</div>'; }
       else
	{ echo '<div id="static-bar" style="visibility:hidden;display:none;" name="carbar" >'; echo barInfo(); echo '</div>'; }
    ?>
  </body>
</html>
