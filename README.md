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
Template: `This is a {{animal}}.`
```php
$mt = new mailTemplate($file = $file);
$mt->vars['animal'] = 'dog';
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
$mt = new mailTemplate($file = $file);
$mt->blocks['block1'] = True;
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
```html
    <p>This is Block2or3</p>
```

