<?php
    namespace Ganti;
    error_reporting(E_ALL);
    use Exception;

    class phpTemplateBlocks{
        public $template;
        public $vars;
        public $blocks;

        function __construct($file = null, $vars = null, $blocks = null){
            @set_exception_handler(array($this, 'template_error'));
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
            $this->output = preg_replace("/(\r\r)/", PHP_EOL, $this->output);
            $this->output = preg_replace("/(\n\n)/", PHP_EOL, $this->output);
            $this->output = preg_replace("/(\r\n\r\n)/", PHP_EOL, $this->output);
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
                    //Missing Endblock
                    $endPattern = 'endblock:'.preg_replace('/^block:/', '', $block);
                    if(in_array($endPattern, $this->templateElements) === False){
                        throw new Exception(sprintf("Block «%s» has no endblock defined!", $block));
                        unset($this->blocks[$pattern]);
                        return False;
                    }
                    //Mixed Outputs
                    $keys = preg_split('/[ ]?,/', preg_replace('/^block:/', '', $block));
                    $firstType = null;
                    foreach($keys as $key){
                        if($firstType === null and ($this->endsWith('_html', $key) or $this->endsWith('_text', $key))){
                            $keyParts = explode('_', $key);
                            $firstType = end($keyParts);
                        }
                        if($firstType !== null){
                            $keyParts = explode('_', $key);
                            if (end($keyParts) !== $firstType and end($keyParts) !== $key){
                                $blockNoType = preg_replace('/(_html|_text)$/', '', $key);
                                throw new Exception(sprintf("Block «%s» mixed outputs, eighter block_text or block_text, but not both!", $block, $blockNoType));
                                return False;
                            }
                        }
                    }
                }
            }
        }

        protected function setBlocksForOutput(){
            foreach($this->templateElements as $block){
                if($this->startsWith('block:', $block)){
                    $block = trim(str_replace('block:','', $block));
                    $aked_keysWithType = preg_split('/[ ]?,/', $block);
                    
                    if(in_array('and', $aked_keysWithType) === True){
                        $operator = 'and';
                        array_diff($aked_keysWithType, array('and','AND'));
                        $showBlock = True;
                    }else{
                        $operator = 'or';
                        array_diff($aked_keysWithType, array('or', 'OR'));
                        $showBlock = False;
                    }

                    foreach($aked_keysWithType as $key){
                        $keykNoType = preg_replace(array('/(_html|_text)$/'), '', $key);
                        if(isset($this->blocks[$keykNoType]) and $this->blocks[$keykNoType] === True){
                            if($operator === 'and'){
                                $showBlock = $showBlock AND True;
                            }else{
                                $showBlock = True;
                            }
                        }else{
                            $showBlock = $showBlock AND False;
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

                    $this->modifyBlocksForOutput($block, $showBlock);
                }
            }
        }

        protected function modifyBlocksForOutput($block, $showBlock = False){
            if($showBlock === True){
                //Remove block and endblock
                $patterns = array(sprintf("/{{block:%s}}[\r\n]/", $block), sprintf("/[\r\n]{{endblock:%s}}[\r\n]/", $block) );
                $this->output = preg_replace($patterns, '', $this->output);
            }else{
                //Remove whole block
                $pattern = sprintf("/[\r\n]{{block:%s}}(.*?(\n))+.*?{{endblock:%s}}/", $block, $block);
                $this->output = preg_replace($pattern, '', $this->output);
            }
        }
        

        protected function startsWith($needle, $haystack){
            return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
        }

        protected function endsWith($needle, $haystack){
            return substr_compare($haystack, $needle, -strlen($needle)) === 0;
        }

        public function template_error($exception) {
            print "\nTemplateError: ". $exception->getMessage() ."\n\n";
        }
    }