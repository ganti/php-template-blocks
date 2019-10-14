<?php
namespace Ganti\phpTemplateBlocks\Test;

use Ganti\phpTemplateBlocks;
use PHPUnit\Framework\TestCase;

final class varsTest extends TestCase {
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

}