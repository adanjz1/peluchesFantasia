
if (navigator.userAgent.indexOf("Safari") > 0)
{
  isSafari = true;
  isMoz = false;
  isIE = false;
}
else if (navigator.product == "Gecko")
{
  isSafari = false;
  isMoz = true;
  isIE = false;
}
else
{
  isSafari = false;
  isMoz = false;
  isIE = true;
}

function auto_complete()
{
  var popup = null;
  var request = null;
  var inputField = document.getElementById("searcher-input");
  var options = new Array(); 
  var current = 0;
  var query = inputField.value;

  if(query!="")
  {
    sendRequest();
  }
  else
  {
    hidePopup();
  }
    
  function sendRequest()
  {
  
    if (window.XMLHttpRequest)
      request = new XMLHttpRequest();
    if(!window.XMLHttpRequest)
      request = new ActiveXObject("Microsoft.XMLHTTP");

    if(request==null)
      alert("requestuest null");
    
    request.onreadystatechange = processChange;
    request.open("POST","autocomplete.php",true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
    var data = "word="+query;
    request.send(data);
  }
  
  function processChange()
  {
    if(request.readyState == 4)
    {
      if(request.status == 200)
      {
        updatePopup();
      }
    }
  }

  function updatePopup() 
  {
    popup = document.getElementById('searcher-popup');
    if(popup!=null)
    {
      var list = request.responseText.split("|");
      clear(popup);
      if(list.length==1 && list[0]=="")
      {
        hidePopup();
        return;
      }
      
      var pophtml = document.createElement('ul');
      pophtml.style.position = 'relative';
      pophtml.style.marginTop = 0+'px';
      pophtml.style.marginBottom = 0+'px';
      pophtml.style.width = 150+'px';
      pophtml.style.left = 0+'px';
      pophtml.style.height = 300+'px';
      pophtml.style.top = 0+'px';
//       pophtml.style.border = '1px solid green';
      pophtml.style.padding = 10+'px';
//       pophtml.style.z-index = 20;
//       pophtml.tabIndex = 0;
      if(isMoz)
        pophtml.style.overflow = '-moz-scrollbars-vertical';
      
      current = 0;
      for(i=0;i<list.length;++i)
      {
        options[i] = document.createElement('li');
        options[i].setAttribute('style',"font-family:helvetica,sans-serif;font-size:12px;color:#B1B3B4;list-style:none;margin-left:0px;");
        options[i].innerHTML = list[i];
        options[i].index = i;
        addOptionHandlers(options[i]);
        pophtml.appendChild(options[i]);
      }
      popup.appendChild(pophtml);
      addHandler(popup,"keypress",eventOnPopup);
      addKeyListener(inputField, handleInputKeypress);
//       addFocusListener(inputField, hideeee)
      setPopupStyles();
    }
  }
  
  function handleInputKeypress(eventkey)
  {
    if(eventkey.keyCode=='40')
    {
      popup.focus();
    }
  }
  
  function clear(element)
  {
    var childrens = element.childNodes;
    for(i=0;i<childrens.length;++i)
      element.removeChild(childrens[i]);
  }
  
  function setPopupStyles()
  {
    var maxHeight = 400;
    
//     if(popup.offsetHeight < maxHeight)
//     {
//       popup.style.overflow = 'hidden';
//     }
//     else if(isMoz)
//     {
//       popup.style.maxHeight = maxHeight + 'px';
// //       popup.style.overflow = '-moz-scrollbars-vertical';
//     }
//     else
    {
//       popup.style.height = maxHeight + 'px';
      popup.style.overflowX = 'hidden';
      popup.style.overflowY = 'auto';
      popup.style.overflow = 'auto';
    }
    
    popup.style.position = 'absolute';
    popup.style.border = '1px solid #FDCA0D';
    popup.style.MozBorderRadius = 5+'px';
    popup.style.webkitBorderRadius = 5+'px';
    popup.style.left = 0+'px';
    popup.style.top = 	40+'px';
    popup.style.visibility = 'visible';
    popup.style.display = 'inline';
    popup.style.zIndex = 10;
    popup.style.backgroundColor = "#FFFFFF";
//     popup.tabIndex = 0;
  }
  
  function handleClick(e)
  {
//     alert("click");
    inputField.value = eventElement(e).innerHTML;
    popup.style.visibility = 'hidden';
    inputField.focus();
  }
  
  function handleOver(e)
  {
//     alert("over");
    options[current].className = '';
    current = eventElement(e).index;
    options[current].className = 'selected';
  }
    
  function addOptionHandlers(option)
  {
//     alert("option "+option.innerHTML);
    addHandler(option, "click", handleClick);
    addHandler(option, "mouseover", handleOver);
  }
  
  function eventOnPopup(event) 
  {
    if(event.keyCode=='38')	// up
    {
      aux = (current-1)%options.length;;
      if(aux<0)
        current=options.length-1;
      else
        current=aux;
    }
    else if(event.keyCode=='40')	// down
    {
      current=(current+1)%options.length;
    }
    else if((event.keyCode == '13' || ev.keyCode == '9'))// && dv.style.visibility == 'visible')
    {
      hidePopup();
//       inputField.focus();
      if(isIE)
      {
        event.returnValue = false;
      }
      else
      {
        e.preventDefault();
      }
    }
    updateView();
  }

  function updateView()
  {
    var inputField = document.getElementById('searcher-input');
    inputField.value = options[current].textContent;
    for(i=0;i<options.length;i++)
    {
      if(i==current)
      {
        options[current].className = 'selected';
      }
      else
      {
        options[i].className = '';
      }
    }
  }
  
  function hidePopup()
  {
    popup = document.getElementById('searcher-popup');
    popup.style.visibility = 'hidden';
    popup.style.display = 'none';  
  }
  
  function hideeee(e)
  {
    popup = document.getElementById('searcher-popup');
    popup.style.visibility = 'hidden';
    popup.style.display = 'none';  
  }
}

// Browser´s compatibility functions

function addKeyListener(element, handler)
{
  if (isSafari)
    element.addEventListener("keydown",handler,false);
  else if (isMoz)
    element.addEventListener("keypress",handler,false);
  else
    element.attachEvent("onkeydown",handler);
}

function addFocusListener(inputField, handler)
{
  if (isSafari)
    element.addEventListener("blur",handler,false);
  else if (isMoz)
    element.addEventListener("blur",handler,false);
  else
    element.attachEvent("blur",handler);
}

function eventElement(event)
{
  if(isMoz)
  {
    return event.currentTarget;
  }
  else
  {
    return event.srcElement;
  }
}

function addHandler(element, type, handler)
{
  if(element.addEventListener)
  {
    element.addEventListener(type, handler, false);
  }
  else
  {
    element.attachEvent('on' + type, handler);
  }
}

function removeHandler(element, type, handler)
{
  if(element.removeEventListener)
  {
    element.removeEventListener(type, handler, false);
  }
  else
  {
    element.detachEvent('on' + type, handler);
  }
}

function clearProduct(id)
{
  var request=false;
  if (window.XMLHttpRequest)
    request = new XMLHttpRequest();
  if(!window.XMLHttpRequest)
    request = new ActiveXObject("Microsoft.XMLHTTP");

  request.onreadystatechange = function iiiii()
  {
    if(request.readyState == 4)
    {
      if(request.status == 200)
      {
        if(id=="-1")
        {
          var bar = document.getElementById("static-bar");
          var ordered = document.getElementById("carrito");
	  if(ordered!=null)
	    ordered.innerHTML = '<p class="contact-message-success"> No hay ning�n producto agregado al pedido</p>';
          bar.innerHTML = "";
          bar.style.visibility = "hidden";
          bar.style.display = "none";
        }
        else
        {
          var table = document.getElementById("table-product");
          var bar = document.getElementById("static-bar");
          var row = document.getElementById("ordered-product-"+id);
          bar.innerHTML = request.responseText;
          table.deleteRow(row.rowIndex);
          var trs = table.getElementsByTagName("tr");
          if(table.getElementsByTagName("tr").length==1)
          {
            var ordered  = document.getElementById("carrito");
            ordered.innerHTML = '<p class="contact-message-success"> No hay ning�n producto agregado al pedido</p>';
            bar.innerHTML = "";
            bar.style.visibility = "hidden";
            bar.style.display = "none";
          }
        }
      }
    }
  }
  request.open("POST","pedido.php",true);
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  var data = "action=clear&id="+id;
  request.send(data);
}

function confirmar(usuario,id,pname,pdescription,price,cat)
{
  var req=false;
  if (window.XMLHttpRequest)
    req = new XMLHttpRequest();
  if(!window.XMLHttpRequest)
    req = new ActiveXObject("Microsoft.XMLHTTP");
  
  var cc = document.getElementById("product-"+id).value;
  req.onreadystatechange = function readya()
  {
    if(req.readyState == 4)
    {
      if(req.status == 200)
      {
        var bar = document.getElementById("static-bar");
        bar.innerHTML = req.responseText;
        bar.style.visibility = "visible";
        bar.style.display = "inline";
      }
    }
  }
  req.open("POST","pedido.php",true);
  req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  var data = "id="+id+"&nombre="+pname+"&descripcion="+pdescription+"&precio="+price+"&cantidad="+cc+"&categoria="+cat;
  req.send(data);
}

function updateSubmenu(id)
{
  var item=document.getElementById('side-submenu-'+id);
  if(item.style.display=='none')
    item.setAttribute('style','display:inline;');
  else
    item.setAttribute('style','display:none;');
  resizemf();
}

function searchProduct()
{
  var inputField = document.getElementById("searcher-input");
  var frame = document.getElementById("content");
  var url = 'productos.php?products=show&search='+inputField.value;

  document.location = url;
}

function recalculatePrice(id)
{
  var row = document.getElementById("ordered-product-"+id);
  var child = row.childNodes;
  var cc = child[3].childNodes[0].value;
  var subtotal = child[4].innerHTML.replace('$','');
  child[5].innerHTML = '$'+subtotal*cc;
 
  var table = document.getElementById("table-product");
  var rows = table.rows;
  var total;
  for(i=1;i<rows.length;++i)
  {
    if(i==1)
      total = parseFloat(rows[i].cells[5].innerHTML.replace('$',''));
    else  
      total += parseFloat(rows[i].cells[5].innerHTML.replace('$',''));
  }
  
  var req=false;
  if (window.XMLHttpRequest)
    req = new XMLHttpRequest();
  if(!window.XMLHttpRequest)
    req = new ActiveXObject("Microsoft.XMLHTTP");
  
  req.onreadystatechange = function readya()
  {
    if(req.readyState == 4)
    {
      if(req.status == 200)
      {
	var bar = document.getElementById("static-bar");
	bar.innerHTML = req.responseText;
	bar.style.visibility = "visible";
	bar.style.display = "inline";
	var ptotal = document.getElementById("ordered-total");
	ptotal.innerHTML = 'Total $'+total;
      }
    }
  }
  req.open("POST","pedido.php",true);
  req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  var data = "id="+id+"&cantidad="+cc+"&replace=true";
  req.send(data);
}

function resizemf()
{
  var wa = document.getElementById("workarea");
  var products = document.getElementById("products");
  var esmokin = document.getElementById("esmokin");
//   alert("esmokin.offsetTop: "+esmokin.offsetTop+" esmokin.offsetHeight "+esmokin.offsetHeight+" wa.offsetHeight: "+wa.offsetHeight+" wa.offsetTop: "+wa.offsetTop);
  if((esmokin.offsetTop+wa.offsetTop)>wa.offsetHeight)
    wa.style.height = wa.offsetTop+esmokin.offsetTop+'px';
  if(products.offsetHeight>(wa.offsetHeight))
    wa.style.height = products.offsetHeight+30+'px';
}

function checkOrdered(e)
{
  if(orderedform.name.value=='')
  {
    alert("Debe completar el campo Nombre y Apellido");
    cancelEvent(e); 
  }
  else
  {
    if(orderedform.socialreason.value=='')
    {
      alert("Debe completar el campo Razón social");
      cancelEvent(e); 
    } 
    else 
    {
      if(orderedform.tel.value=='')
      {
        alert("Debe completar el campo Teléfono");
        cancelEvent(e);
      }
      else
      {
        if(orderedform.email.value=='')
        {
          alert("Debe completar el campo E-mail");
          cancelEvent(e);
        }
        else
        {
          if(orderedform.cativa.value=='')
          {
            alert("Debe completar el campo Categoria IVA")
            cancelEvent(e);
          }
          else
          {
            if(orderedform.cuit.value=='')
            {
              alert("Debe completar el campo CUIT")
              cancelEvent(e);
            }
            else
            {
              if(orderedform.savedata.checked)
              {
                if(orderedform.username.value=='')
                {
                  alert("Debe completar el campo nombre de usuario");
                  cancelEvent(e);
                }
                else
                {
                  if((orderedform.password.value=='') || (ordered.cpassword.value=='') || (orderedform.password.value!=orderedform.cpassword.value))
                  {
                    alert("Las contraseñas deben no ser nulas y coincidir");
                    cancelEvent(e);
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}

function cancelEvent(e)
{
  if(window.event)
  {
    e.returnValue=false;
  }
  else
  {
    e.preventDefault();
  }
}
