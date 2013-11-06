
function changeContent(v)
{
  $('#content').remove()
  $('body').append( "<iframe id='content' src='content.php?show="+v+"' scrolling='no'></iframe>" );
  var l = document.getElementById("add-link");
  if(v=="user")
    l.innerHTML='Agregar Usuario';
  if(v=="subcategory")
    l.innerHTML='Agregar Subcategoria';
  if(v=="product")
    l.innerHTML='Agregar Producto';  
  highlightItem(v);
}

function highlight(obj)
{
  var table = obj.parentNode;
  var rows = table.rows;
  for(i=0;i<rows.length;++i)
  {
    if(rows[i].cells[0].className=="content-table-cell-selected")
    {
      for(j=0;j<rows[i].cells.length;++j)
      {
	rows[i].cells[j].className= "content-table-cell";
      }
    }  
  }
  for(i=0;i<obj.cells.length;++i)
  {
    obj.cells[i].className = "content-table-cell-selected";
  }
}

function highlightList(obj)
{
  var parent = obj.parentNode;
  var lis = parent.getElementsByTagName("li");
  for(i=0;i<lis.length;i++)
  {
    lis[i].className = "content-list";  
  }
  
  if(obj.className == "content-list")
    obj.className = "content-list-selected";
  else
    obj.className = "content-list";
}

function highlightItem(item)
{
  var table = document.getElementById("item-table");
  var obj = null;
  if(item=="user")
    obj = table.rows[0].cells[0];
  if(item=="subcategory")
    obj = table.rows[0].cells[1];
  if(item=="product")
    obj = table.rows[0].cells[2];
  var row = obj.parentNode;
  for(i=0;i<row.cells.length;++i)
    row.cells[i].className="item-unselected";
  obj.className= "item-selected";
  clearHighlightAction();
}

function highlightAction(taction)
{
  console.log(taction)
  var table = document.getElementById("action-table");
  var obj = null;
  if(taction=="edit")
    obj = table.rows[0].cells[0];
  if(taction=="remove")
    obj = table.rows[0].cells[1];
  if(taction=="add")
    obj = table.rows[0].cells[2];
  
  var row = obj.parentNode;
  for(i=0;i<row.cells.length;++i)
    row.cells[i].className="action-unselected";
  obj.className= "action-selected";
}

function clearHighlightAction()
{
  var row = document.getElementById("action-table").rows[0];
  for(i=0;i<row.cells.length;++i)
    row.cells[i].className="action-unselected";
}

function findRowSelected()
{
  var val = getValue();
  var content = document.getElementById("content");
  var table = content.contentDocument.getElementById(val+"-table");
  var rows = table.rows;
  for(i=1;i<rows.length;++i)
  {
    if(rows[i].cells[0].className=="content-table-cell-selected")
    {
      return rows[i];
    }  
  }
  return null;
}

function findItemSelected()
{
  var content = document.getElementById("content");
  var list = content.contentDocument.getElementById("subcategory-list");
  var items = list.getElementsByTagName("li");
  for(i=0;i<items.length;++i)
  {
    if(items[i].className=="content-list-selected")
    {
      return items[i];
    }  
  }
  return null;  
}

function getValue()
{
  var content = document.getElementById("content");
  var s = content.contentDocument.getElementById("product-table");
  typee = 'user';
console.log(typee)
console.log(s)
console.log(content)
  if(s)
    typee='product';
  s = content.contentDocument.getElementById("user-table");
  if(s)
    typee='user';
  s = content.contentDocument.getElementById("subcategory-list");
  if(s)
    typee='subcategory';
  return typee;    
}

function add()
{
  highlightAction("add");
  var content = document.getElementById("content");
  var url = 'content.php?add=';
  url+=getValue();
  content.contentDocument.location = url;
}

function edit()
{
  var s = getValue();
  if(s!="subcategory")
  {
	console.log(findRowSelected());
    var row = findRowSelected();
    if(row==null || s==null)
    {
      alert("Por favor, seleccione una fila para editar");
      return;
    }
    highlightAction("edit");
    var args;
    var cells = row.getElementsByTagName("td");
    if(s=="user")
    {
      args='&id='+$(cells[0]).html()+'&username='+$(cells[1]).html()+'&password='+$(cells[2]).html();
    }
    else if(s=="product")
    {
      args='&id='+$(cells[0]).html()+'&code='+$(cells[1]).html()+'&name='+$(cells[2]).html()+'&description='+$(cells[3]).html()+'&price='+$(cells[4]).html()+'&image='+$(cells[5]).html()+'&subcategory='+$(cells[6]).html()+'&exclusive='+$(cells[7]).html()+'&video='+$(cells[8]).html()+'&idcategory='+$(cells[9]).html()+'&idsubcat='+$(cells[10]).html();
    }
  }
  else
  {
    var item = findItemSelected();
    if(item==null || s==null)
    {
      alert("Por favor, seleccione un item para editar");
      return;
    }
    var vvv = item.id.split("_");
    args='&id='+vvv[1]+'&name='+item.innerHTML;
  }
  var content = document.getElementById("content");
  var url = 'content.php?edit='+s+args;
  content.contentDocument.location = url;
}

function remover()
{
  var s =getValue();
console.log(s)
  if(s!="subcategory")
  {
    var row = findRowSelected();
    if(row==null || s==null)
    {
      alert("Por favor, seleccione una fila para borrar");
      return;
    }
    var cells = row.getElementsByTagName("td");
    var args = s+'&id='+cells[0].innerHTML;   
  }
  else
  {
    var item = findItemSelected();
    if(item==null || s==null)
    {
      alert("Por favor, seleccione un item para editar");
      return;
    }
    var vvv = item.id.split("_");
    args=s+'&id='+vvv[1];    
  }
  highlightAction("remove");
  if(confirm("Estas seguro de borrarlo?"))
  {  
    var url = 'content.php?remove='+args;
    var content = document.getElementById("content");
    content.contentDocument.location = url;
  }
}

function selectSubcategory(showProducts)
{
  var category = document.getElementById("category-select");
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
	if(category.value!="-1")
	{
	  var sc = document.getElementById("subcategory-select");
	  sc.innerHTML = req.responseText;
	}
	else
	{
	  var container = document.getElementById("subcategory-container");
	  container.innerHTML = req.responseText;	
	}
      }
    }
  }
  req.open("POST","pedido.php",true);
  req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  if(category.value!="-1")
    var data = "request=product&category="+category.value+"&show="+showProducts;
  else
    var data = "request=product&subcategory="+category.value+"&show="+showProducts;
  req.send(data);  
}

function showProducts()
{
  var subcategory = document.getElementById("subcategory-select");
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
	var container = document.getElementById("subcategory-container");
	container.innerHTML = req.responseText;
      }
    }
  }
  req.open("POST","pedido.php",true);
  req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  var data = "request=product&subcategory="+subcategory.value;
  req.send(data);
}

function showSubcategory()
{
  var category = document.getElementById("category-select");
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
	var sc = document.getElementById("subcategory-container");
	sc.innerHTML = req.responseText;
        var scripts = document.getElementsByTagName("script");
        for(var i=0;i<scripts.length;i++)
        {
          if(scripts[i].firstChild!=null)
          {
            var script = scripts[i].firstChild.nodeValue
            if(script != null)
            {
              eval(script)
            }
          }
        }	
      }
    }
  }
  req.open("POST","pedido.php",true);
  req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  var data = "request=subcategory&category="+category.value;
  req.send(data);  
}
