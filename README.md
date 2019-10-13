# phpTemplateBlocks
Simple Template Class, e.g. for eMail Template


## Usage
### Example
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

    $t = new phpTemplateBlocks($file = $file, $vars = $vars, $blocks = $blocks);
    $output =  $t->getOutput();
    echo $output;
```

### Output as HTML or text
```php
$t->getOutput();        //Output as HTML
$t->getOutput('html');  //Output as HTML
$t->getOutputHTML();    //Output as HTML

$t->getOutput('text');  //Output as text
$t->getOutputText();    //Output as text
```

### add vars and blocks after creating an instance
```php
$mt = new phpTemplateBlocks($file = $file);
$mt->vars['var1'] = 'var1xxx';
$mt->blocks['block1'] = True;
```

### Variable replacement
Template: `This is a {{animal}}.`
```php
$t = new phpTemplateBlocks($file = $file);
$t->vars['animal'] = 'dog';
```

Output: 
```html
This is a dog.
```

## Show / Hide Blocks
Template:
```html
{{block:block1}}
    <p>This is block1</p>
{{endblock:block1}}
```
```php
$t = new phpTemplateBlocks($file = $file);
$t->blocks['block1'] = True;
```

Output:
```html
    <p>This is block1</p>
```

## Combined Blocks
Combined blocks allow to show a block if one of the keys are `true`

Template:
```html
{{block:block2,block3}}
    <p>This is Block2or3</p>
{{endblock:block2,block3}}
```
```php
$mt = new mailTemplate($file = $file);
$mt->blocks['block2'] = False;
$mt->blocks['block3'] = True;
```

Output:
```html
    <p>This is Block2or3</p>
```

