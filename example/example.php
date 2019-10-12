<?php
    namespace Ganti;
    require_once(__DIR__.'/../src/phpTemplateBlocks.php');

    $file = __DIR__.'/example.html';
    $vars = array(  'name' => 'Hal',
                    'var2' => 'Foo',
                    'var3' => 'Foobar'
    );
    $blocks = array('block1' => True,
                    'block2' => False,
                    'block3' => True
    );

    $t = new phpTemplateBlocks($file = $file, $vars = $vars, $blocks = $blocks);
    $output =  $t->getOutput();
    echo $output;


/*
    $t = new mailTemplate($file = $file);
    $t->vars['var1'] = 'var1xxx';
    $t->vars['var2'] = 'var2xxx';
    $t->vars['var3'] = 'var3xxx';
    $t->blocks['block1'] = False;
    $t->blocks['block2'] = True;
    $t->blocks['block3'] = False;
    echo $t->getOutput();
*/