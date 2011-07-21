<?php
class Traducao extends TraducaoAppModel{
	var $name = 'Traducao';
   	var $useTable = 'dicionarios';

	function import($filename) {

	  if (empty($filename)) $filename='default.pot';
	  $filename = ROOT . DS . 'app' . DS. 'locale' . DS . $filename;
	  //$filename= '/app/locale/default.pot';
	
	  $filehandle = fopen($filename, "r");
	  while (($row = fgets($filehandle)) !== FALSE) {
		 if (substr($row,0,7) == 'msgid "') {
			// parse string in hochkomma:
			$msgid = substr($row, 7 ,(strpos($row,'"',6)-8));
			if (!empty($msgid)) {
			   $row = fgets($filehandle);
			   if (substr($row,0,8) == 'msgstr "') {
				  $msgstr = substr($row, 8 ,(strpos($row,'"',7)-9));
			   }
			   $trec = $this->find(array("Traducao.msgid" =>$msgid, "locale"=>"pt-br"));
			   if (empty($trec)) {
				  $this->create();
				  $this->data['Traducao']['msgid'] = $msgid;
				  $this->data['Traducao']['msgstr'] = $msgstr;
				  $this->data['Traducao']['locale'] = 'pt-br';
				  $this->data['Traducao']['status'] = 'n';
				  $this->save($this->data);
			   } 
			}
		 }
	  }
	  fclose($filehandle);
	}

  
	
}
?>