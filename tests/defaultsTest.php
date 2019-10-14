<?php
namespace Ganti\phpTemplateBlocks\Test;

use Ganti\phpTemplateBlocks;
use PHPUnit\Framework\TestCase;

final class phpTemplateBlocksTest extends TestCase {

    
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

    public function testVarSubstitution(){
        $file = __DIR__.'/test.html';
        $vars = array(  'name' => 'Hal',
                        'var2' => 'Foo',
                        'var3' => 'Foobar'
        );
        $t = new phpTemplateBlocks($file = $file, $vars = $vars, $blocks = null);
        $this->assertStringNotContainsString('Hallo {{name}}', $t->getOutput('html'));
        $this->assertStringContainsString('Hallo Hal', $t->getOutput('html'));

        $this->assertStringNotContainsString('says {{var2 }} and', $t->getOutput('html'));
        $this->assertStringContainsString('says Foo and', $t->getOutput('html'));

        $this->assertStringNotContainsString('<strong>{{ var3 }}</strong>', $t->getOutput('html'));
        $this->assertStringContainsString('<strong>Foobar</strong>', $t->getOutput('html'));
    }

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

    public function testMixedBlocksFail(){
        try{
            $fail = False;
            $msg = 'ThisIsEmpty';
            $t = new phpTemplateBlocks();
            $t->template = '
                foo
                foo
                {{block:block2_html, block3_text}}
                <p>This is Block2or3</p>
                {{endblock:block2_html, block3_text}}
                fooo
            ';
            $t->getOutput();
        }
        catch(\Exception $e)
        {
            $fail = True;
            $msg = $e->getMessage();
        }
        $this->assertTrue($fail);
        $this->assertStringNotContainsString('ThisIsEmpty',$msg);
        $this->assertStringContainsString('mixed outputs, eighter block_text or block_text, but not both!',$msg);
        if(!$fail) $this->fail("No Exceptions were thrown.");
        
    }

    public function testMixedBlocksAllow(){
        try{
            $fail = False;
            $msg = 'ThisIsEmpty';
            $t = new phpTemplateBlocks();
            $t->template = '
                foo
                foo
                {{block:block2, block3_text}}
                <p>This is Block2or3</p>
                {{endblock:block2, block3_text}}
                fooo
            ';
            $t->getOutput();
        }
        catch(\Exception $e)
        {
            $fail = True;
            echo $msg = $e->getMessage();
        }
        $this->assertTrue($fail === False);
        $this->assertStringContainsString('ThisIsEmpty',$msg);
        $this->assertStringNotContainsString('mixed outputs, eighter block_text or block_text, but not both!',$msg);
        if($fail) $this->fail("Exception were thrown.");
    }

    public function testMissingEndblock(){
        try{
            $fail = False;
            $msg = 'ThisIsEmpty';
            $t = new phpTemplateBlocks();
            $t->template = '
                foo
                foo
                {{block:block2, block3_text}}
                <p>This is Block2or3</p>
                {{endblock:}}
                fooo
            ';
            $t->getOutput();
        }
        catch(\Exception $e)
        {
            $fail = True;
            $msg = $e->getMessage();
        }
        $this->assertTrue($fail);
        $this->assertStringNotContainsString('ThisIsEmpty',$msg);
        $this->assertStringContainsString('has no endblock defined!',$msg);
        if(!$fail) $this->fail("No Exception were thrown.");
    }

}