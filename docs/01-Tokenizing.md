Tokenizing
==========

Creating tokens from a docblock with zero or more annotations is simple.

```php
use Herrera\Annotations\Convert;
use Herrera\Annotations\Tokenize;

$tokenize = new Tokenize();

$docblock = <<<DOCBLOCK
/**
 * @ORM\JoinTable(
 *     name="myJoinTable",
 *     joinColumns={
 *         @ORM\JoinColumn(name="columnA", referencedColumnName="columnB")
 *     },
 *     inverseJoinColumns={
 *         @ORM\JoinColumn(name="columnC", referencedColumnName="columnD", unique=true)
 *     }
 * )
 */
DOCBLOCK;

$tokens = $tokenize->parse($docblock);
// instance can be re-used
```

In this example, the `$tokens` array would contain the following tokens:

```php
array(
    array(101),
    array(100, 'ORM\\JoinTable'),
    array(109),
    array(100, 'name'),
    array(105),
    array(3, 'myJoinTable'),
    array(104),
    array(100, 'joinColumns'),
    array(105),
    array(108),
    array(101),
    array(100, 'ORM\\JoinColumn'),
    array(109),
    array(100, 'name'),
    array(105),
    array(3, 'columnA'),
    array(104),
    array(100, 'referencedColumnName'),
    array(105),
    array(3, 'columnB'),
    array(103),
    array(102),
    array(104),
    array(100, 'inverseJoinColumns'),
    array(105),
    array(108),
    array(101),
    array(100, 'ORM\\JoinColumn'),
    array(109),
    array(100, 'name'),
    array(105),
    array(3, 'columnC'),
    array(104),
    array(100, 'referencedColumnName'),
    array(105),
    array(3, 'columnD'),
    array(104),
    array(100, 'unique'),
    array(105),
    array(110, 'true'),
    array(103),
    array(102),
    array(103)
)
```

Tokens
------

Tokens are defined by the `Doctrine\Common\Annotations\DocLexer` class:

```php
final class DocLexer extends AbstractLexer
{
    const T_NONE                = 1;
    const T_INTEGER             = 2;
    const T_STRING              = 3;
    const T_FLOAT               = 4;

    const T_IDENTIFIER          = 100;
    const T_AT                  = 101;
    const T_CLOSE_CURLY_BRACES  = 102;
    const T_CLOSE_PARENTHESIS   = 103;
    const T_COMMA               = 104;
    const T_EQUALS              = 105;
    const T_FALSE               = 106;
    const T_NAMESPACE_SEPARATOR = 107;
    const T_OPEN_CURLY_BRACES   = 108;
    const T_OPEN_PARENTHESIS    = 109;
    const T_TRUE                = 110;
    const T_NULL                = 111;
    const T_COLON               = 112;
}
```
