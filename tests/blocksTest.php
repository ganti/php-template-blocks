<?php
namespace Ganti\phpTemplateBlocks\Test;

use Ganti\phpTemplateBlocks;
use PHPUnit\Framework\TestCase;

final class blocksTest extends TestCase {

    public function testBlockBasicsHTML(){
        $file = __DIR__.'/test.html';
        $blocks = array('block1' => True, 'block2' => True, 'block3' => True);
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = $blocks);

        $notFound = array(  "{{block:block1}}",
                            "{{endblock:block1}}",
                            "{{block:block2,block3}}",
                            "{{endblock:block2,block3}}",
                            "{{block:or,block2_html,block3}}",
                            "{{endblock:or,block2_html,block3}}",
                            "{{block:and,block1,block2,block3}}",
                            "{{endblock:and,block1,block2,block3}}",
                            "<p>This is block1_text</p>",
                            "<p>This is Block2or3_text</p>"
                        );
        $found = array( "<p>This is block1",
                        "<p>This is block1_html",
                        "<p>This is Block2or3_html</p>\n",
                        "<p>This is Block2and3_html</p>\n"
        );
        foreach($notFound as $v){
            $this->assertStringNotContainsString($v, $t->getOutput('html'));
        }
        foreach($found as $v){
            $this->assertStringContainsString($v, $t->getOutput('html'));
        }
    }

    public function testBlockBasicsText(){
        $file = __DIR__.'/test.html';
        $blocks = array('block1' => True, 'block2' => True, 'block3' => True);
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = $blocks);

        $notFound = array(  "<p>This is block1_html</p>",
                            "<p>This is Block2or3_html</p>",
                            "<p>This is Block2and3_html</p>",
                        );
        $found = array( "This is block1\n",
                        "This is block1_text\n",
                        "This is Block2or3_text\n",
                        "This is Block2and3_text\n"
        );
        foreach($notFound as $v){
            $this->assertStringNotContainsString($v, $t->getOutput('text'));
        }
        foreach($found as $v){
            $this->assertStringContainsString($v, $t->getOutput('text'));
        }
    }

    public function testBlockBasicsOR(){
        $file = __DIR__.'/test.html';
        $t = new phpTemplateBlocks($file = $file);

        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => True, 'block3' => True));
        $this->assertStringContainsString("<p>This is Block2or3</p>", $t->getOutput('html'));
        $this->assertStringContainsString("<p>This is Block2or3_html", $t->getOutput('html'));
        
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => False, 'block3' => True));
        $this->assertStringContainsString("<p>This is Block2or3</p>", $t->getOutput('html'));
        $this->assertStringContainsString("<p>This is Block2or3_html", $t->getOutput('html'));
        
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => True, 'block3' => False));
        $this->assertStringContainsString("<p>This is Block2or3</p>", $t->getOutput('html'));
        $this->assertStringContainsString("<p>This is Block2or3_html", $t->getOutput('html'));

        $t = new phpTemplateBlocks($file = $file, $blocks = array('block2' => False, 'block3' => False));
        $this->assertStringNotContainsString("<p>This is Block2or3", $t->getOutput('text'));
        $this->assertStringNotContainsString("<p>This is Block2or3_text", $t->getOutput('text'));
        
    }

    public function testBlockBasicsAND(){
        $file = __DIR__.'/test.html';
        
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block1' => True, 'block2' => True, 'block3' => True));
        $this->assertStringContainsString("This is Block2and3_text", $t->getOutput('text'));

        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block1' => True, 'block2' => True, 'block3' => False));
        $this->assertStringNotContainsString('Block1,2and3', $t->getOutput('text'));

        $check ="This is Block2and3_text\n";
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => False, 'block3' => False));
        $this->assertStringNotContainsString($check, $t->getOutput('text'));
        
        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => True, 'block3' => False));
        $this->assertStringNotContainsString($check, $t->getOutput('text'));

        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => False, 'block3' => True));
        $this->assertStringNotContainsString($check, $t->getOutput('text'));

        $t = new phpTemplateBlocks($file = $file, $vars = null, $blocks = array('block2' => True, 'block3' => True));
        $this->assertStringContainsString($check, $t->getOutput('text'));

    }
}