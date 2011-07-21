<?php
class TraducaoController extends TraducaoAppController {

	public $name = 'Traducao';
   	public $uses = 'Traducao.Traducao';   	
   	public $components = array('Traducao.Traduzir');   	
   	public $langarr = array('en'=>'eng','es'=>'spa');
   	public $defaultLang = 'pt-br'; 
   	//Configure::read("Config.language");
   	
	public function beforeFilter(){
		
	}
   	public function passo1() {
       // Passo 1: importar default.pot
       $this->Traducao->import('default.pot');
   }

	public	function passo2() {
      // Step 2: traduzir todas as linguagens em $langarr

      $trec = $this->Traducao->findAllByLocale($this->defaultLang);
      foreach ($trec as $rec):
         foreach ($this->langarr as $k => $v):
            $tmprec = $this->Traducao->find('all',array('conditions' => array('Traducao.locale' => $k,'Traducao.msgid'=> $rec['Traducao']['msgid'])));
            if (count($tmprec) == 0) {
               $this->data['Traducao']['msgstr'] = $this->Traduzir->translate($rec['Traducao']['msgid'], $this->defaultLang, $k);
               $this->data['Traducao']['msgid'] = $rec
['Traducao']['msgid'];
               $this->data['Traducao']['locale'] = $k;
               $this->data['Traducao']['status'] = 'm';
               $this->Traducao->save($this->data);
            }
         endforeach;

      endforeach;
   }
   public function passo3() {
      // Passo 3: exportar default.po arquivo para pasta correta.
      $filename= 'f' . gmdate('YmdHis');
      foreach ($this->langarr as $k => $v):

         $path = ROOT.DS.'app'.DS.'locale'.DS.$v;
         if (!file_exists($path)) mkdir($path);
         	$path .= DS.'LC_MESSAGES';
         if (!file_exists($path)) mkdir($path);
         	$file = $path.DS.$filename;
         if (!file_exists($path)) touch($file);

         $file = new File($path.DS.$filename);
         $tmprec = $this->Traducao->find('all',array('conditions' => array('Traducao.locale' => $k)));
         foreach ($tmprec as $rec):
            $file->write('msgid "' .$rec['Traducao']['msgid'] .'"'."\n");
            $file->write('msgstr "'.$rec['Traducao']['msgstr'].'"'."\n");
         endforeach;
         $file->close();

         if (file_exists($path.DS.'default.po'))
             rename ($path.DS.'default.po',$path.DS.'default.po.old'.gmdate('YmdHis'));
	         
         rename ($path.DS.$filename,$path.DS.'default.po');
      endforeach;
   }

   
}
?> 