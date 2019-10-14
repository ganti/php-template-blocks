<?php
namespace Ganti\phpTemplateBlocks\Test;

use Ganti\phpTemplateBlocks;
use PHPUnit\Framework\TestCase;

final class blocksValidTest extends TestCase {

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