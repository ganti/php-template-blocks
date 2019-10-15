# phpTemplateBlocks
Simple Template Class, e.g. for eMail Template

## Features

- Substitution/replacement of variables
- Showing/Hiding content blocks with conditions OR, AND
- Output Text as HTML or Text, with one templatefile.

## Usage
### Install
`composer require ganti/php-template-blocks`

### Example`
```html
<p> Hallo {{name}}</p>

{{block:block1}}
  <p>This is block1, shown in HTML and text</p>
{{endblock:block1}}

{{block:block1_html}}
  <p>This is block1, shown only in HTML</p>
{{endblock:block1_html}}

{{block:block1_text}}
  <p>This is block1_text, shown only in text</p>
{{endblock:block1_text}}

{{block:and,block1,block2,block3}}
  <p>This is Block1,2and3, shown only if block1, block2 and block3 are true</p>
{{endblock:and,block1,block2,block3}}

{{block:and,block2_text,block3}}
  <p>This is Block2or3_text, shown only in text if block2 and block3 are true</p>
{{endblock:or,block2_text,block3}}
```

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

### Condition OR (default)
```html
{{block:or,block2,block3}}
  <p>This is Block2or3, shown only in text if block2 or block3 are true</p>
{{endblock:or,block2,block3}}
```

### Condition AND
```html
{{block:and,block2_text,block3}}
  <p>This is Block2and3_text, shown only in text if block2 and block3 are true</p>
{{endblock:and,block2_text,block3}}
```
