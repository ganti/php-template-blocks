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

    $mt = new mailTemplate($file = $file, $vars = $vars, $blocks = $blocks);
    echo $mt->getOutput();


/*
    $mt = new mailTemplate($file = $file);
    $mt->vars['var1'] = 'var1xxx';
    $mt->vars['var2'] = 'var2xxx';
    $mt->vars['var3'] = 'var3xxx';
    $mt->blocks['block1'] = False;
    $mt->blocks['block2'] = True;
    $mt->blocks['block3'] = False;
    echo $mt->getOutput();
*/