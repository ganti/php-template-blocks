<?php
namespace Ganti\phpTemplateBlocks\Test;

use Ganti\phpTemplateBlocks;
use PHPUnit\Framework\TestCase;

final class basicTest extends TestCase {

    
    public function testSetup(){
        $file = __DIR__.'/test.html';

        $t = new phpTemplateBlocks($file = $file);
        $output =  $t->getOutput('html');
        $this->assertStringStartsWith('<p>--- Vars ---</p>', $output);
        $this->assertStringEndsWith('<p>--- EOF ---</p>', $output);
    }

    public function testOutput(){
        $file = __DIR__.'/test.html';

        $t = new phpTemplateBlocks($file = $file);
        foreach(array($t->getOutput(), $t->getOutput('html'), $t->getOutputHTML()) as $o){
            $this->assertStringStartsWith("<p>--- Vars ---</p>", $o);
            $this->assertStringEndsWith("<p>--- EOF ---</p>", $o);
        }

        foreach(array($t->getOutput('text'), $t->getOutputText()) as $o){
            $this->assertStringStartsWith("--- Vars ---", $o);
            $this->assertStringEndsWith("--- EOF ---\n", $o);

            foreach(array("<br>", "<p>") as $v){
                $this->assertStringNotContainsString($v, $o);
            }
            
            $this->assertStringContainsString("\n", $o);
        }
    }

}