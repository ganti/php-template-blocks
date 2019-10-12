# phpTemplateBlocks
Simple Template Class, e.g. for eMail Template


## Usage
### add vars and blocks when creating the instance
```php
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
    $output =  $mt->getOutput();
    echo $output;
```

### add vars and blocks after creating an instance
```php
$mt = new mailTemplate($file = $file);
$mt->vars['var1'] = 'var1xxx';
$mt->blocks['block1'] = True;
```

### Variable replacement
Source: `This is a {{animal}}.`
```php
$mt = new mailTemplate($file = $file);
$mt->vars['animal'] = 'dog';
```

Output: `This is a dog.`
