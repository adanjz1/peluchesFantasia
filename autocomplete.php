<?
  include('db.class.php');
  $pattern = "%".$_REQUEST["word"]."%";
  $results = new dbQuery("select p.code from product p where code like '$pattern'");
  if($results->NumRows()>0)
  {
    while($results->NextValue())
      $value.=$results->result["CODE"]."|";
    echo trim($value,"|");
  }
  else
  {
    $results = new dbQuery("select p.name from product p where name like '$pattern'");
    while($results->NextValue())
      $value.=$results->result["NAME"]."|";
    echo trim($value,"|");
  }
?>