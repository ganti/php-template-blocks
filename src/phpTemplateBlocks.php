<?php
    namespace Ganti;
    error_reporting(E_ALL);
    use Exception;

    class phpTemplateBlocks{
        public $template;
        public $vars;
        public $blocks;

        function __construct($file = null, $vars = null, $blocks = null){
            $this->templateElements = array();
            $this->templateVars = array();
            $this->templateBlocks = array();
            $this->vars = null;
            $this->blocks = null;
            $this->output = null;
            $this->outputType = null;
            
            if($file != null){
                $this->loadTemplateFile($file);
            }
            if($vars != null){
                $this->vars = $vars;
            }
            if($blocks != null){
                $this->blocks = $blocks;
            }
            return;
        }

        public function loadTemplateFile($file){
            if(file_exists($file)){
                $content = file_get_contents($file);
                if($content != False){
                    $this->template = $content;
                    $this->output = $content;
                    $return = True;
                }else{
                    $return = False;
                }
            }else{
                $return = False;
            }
            return $return;
        }

        public function compileOutput(){
            $this->findAllSubstitutionKeys();
            $this->substiututeTemplateVars();
            $this->BlocksSanityCheck();
            $this->setBlocksForOutput();
            return $this->output;
        }

        public function getOutput($outputType = null){
            if($outputType == 'text'){
                $return = $this->getOutputText();
            }else{
                $return = $this->getOutputHTML();
            }
            return $return;
        }

        public function getOutputHTML(){
            $this->outputType = 'html';
            return $this->compileOutput();
        }

        public function getOutputText(){
            $this->outputType = 'text';
            $outputHTML = $this->compileOutput();
            $html = new \Html2Text\Html2Text($outputHTML);
            return $html->getText();
        }

        public function findAllSubstitutionKeys(){
            $pattern = '/{{[ ]?(.+?)[ ]?}}/';
            preg_match_all($pattern, $this->template, $matches);
            $this->templateElements = $matches[1];

            foreach($this->templateElements as $key){
                if($this->startsWith('block:', $key) === True){
                    $this->templateBlocks[] = str_replace('block:', '', $key);
                }
                if($this->startsWith('block:', $key) === False and $this->startsWith('endblock:', $key) === False){
                    $this->templateVars[] = $key;
                }
            }
        }

        protected function substiututeTemplateVars(){
            foreach($this->vars as $var => $value){
                $pattern = '/{{[ ]?'.$var.'[ ]?}}/';
                $this->output = preg_replace($pattern, $value, $this->output);
            }
        }

        protected function BlocksSanityCheck(){
            foreach($this->templateElements as $block){
                if($this->startsWith('block:', $block)){
                    $endPattern = 'endblock:'.str_replace('block:','', $block);
                    if(in_array($endPattern, $this->templateElements) === False){
                        try{
                            throw new Exception(sprintf("Block «%s» has no endblock defined!", $block));
                        }
                        finally{
                            unset($this->blocks[$pattern]);
                        }
                    }
                }
            }
        }

        protected function setBlocksForOutput(){
            foreach($this->templateElements as $block){
                if($this->startsWith('block:', $block)){
                    $showBlock = False;
                    $block = trim(str_replace('block:','', $block));
                    $blockNoType = preg_replace(array('/(_html|_text)$/'), '', $block);

                    $aked_keysWithType = preg_split('/[ ]?,/', $block);
                    $aked_keysNoType = preg_split('/[ ]?,/', $blockNoType);
                    
                    foreach($aked_keysNoType as $key){
                        if(isset($this->blocks[$key]) and $this->blocks[$key] === True){
                            $showBlock = True;
                        }
                    }

                    if($showBlock === True){
                        foreach($aked_keysWithType as $key){
                            if(strpos($key, '_') !== False){
                                if($this->endsWith('_'.$this->outputType, $key) === False){
                                    $showBlock = False;
                                }
                            }
                        } 
                    }

                    if($showBlock === True){
                        //Remove block and endblock
                        $patterns = array(sprintf("/[\r\n]{{block:%s}}/", $block), sprintf("/[\r\n]{{endblock:%s}}/", $block) );
                        $this->output = preg_replace($patterns, '', $this->output);
                    }else{
                        //Remove whole block
                        $pattern = sprintf("/[\r\n]{{block:%s}}(.*?(\n))+.*?{{endblock:%s}}/", $block, $block);
                        $this->output = preg_replace($pattern, '', $this->output);
                    }
                }
            }
        }
        

        protected function startsWith($needle, $haystack){
            return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
        }

        protected function endsWith($needle, $haystack){
            return substr_compare($haystack, $needle, -strlen($needle)) === 0;
        }
    }