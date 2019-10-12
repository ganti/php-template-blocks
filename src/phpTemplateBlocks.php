<?php

    namespace Ganti;
    use Exception;

    class mailTemplate {
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

        public function getOutput(){
            $this->findAllSubstitutionKeys();
            $this->substiututeTemplateVars();
            $this->BlocksSanityCheck();
            $this->setBlocksForOutput();
            return $this->output;
        }

        public function findAllSubstitutionKeys(){
            $pattern = '/{{[ ]?(.+?)[ ]?}}/';
            preg_match_all($pattern, $this->template, $matches);
            $this->templateElements = $matches[1];

            foreach($this->templateElements as $key){
                if($this->startsWith($key, 'block:') === False and $this->startsWith($key, 'endblock:') === False){
                    $this->templateVars[] = $key;
                }
                if($this->startsWith($key, 'block:') === True){
                    $this->templateBlocks[] = str_replace('block:', '', $key);
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
                    $aked_keys = preg_split('/[ ]?,/', $block);
                    foreach($aked_keys as $key){
                        if($this->blocks[$key] === True){
                            $showBlock = True;
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
        
        protected function startsWith($startString, $string){ 
            $len = strlen($startString); 
            return (substr($string, 0, $len) === $startString); 
        }
        
    }