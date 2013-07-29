Annotations
===========

This library will generate tokens from [Doctrine annotations][]. Unlike the
original Doctrine [Annotations][] library, it is not required that any of the
annotation classes be loaded, or even present since no class name or constant
name validation is performed.

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

/*
 * array(
 *     array(101),
 *     array(100, 'ORM\\JoinTable'),
 *     array(109),
 *     array(100, 'name'),
 *     array(105),
 *     array(3, 'myJoinTable'),
 *     array(104),
 *     array(100, 'joinColumns'),
 *     array(105),
 *     array(108),
 *     array(101),
 *     array(100, 'ORM\\JoinColumn'),
 *     array(109),
 *     array(100, 'name'),
 *     array(105),
 *     array(3, 'columnA'),
 *     array(104),
 *     array(100, 'referencedColumnName'),
 *     array(105),
 *     array(3, 'columnB'),
 *     array(103),
 *     array(102),
 *     array(104),
 *     array(100, 'inverseJoinColumns'),
 *     array(105),
 *     array(108),
 *     array(101),
 *     array(100, 'ORM\\JoinColumn'),
 *     array(109),
 *     array(100, 'name'),
 *     array(105),
 *     array(3, 'columnC'),
 *     array(104),
 *     array(100, 'referencedColumnName'),
 *     array(105),
 *     array(3, 'columnD'),
 *     array(104),
 *     array(100, 'unique'),
 *     array(105),
 *     array(110, 'true'),
 *     array(103),
 *     array(102),
 *     array(103)
 * )
 */

echo Convert::toString($tokens);

// @ORM\JoinTable(name="myJoinTable",joinColumns={@ORM\JoinColumn(name="columnA",referencedColumnName="columnB")},inverseJoinColumns={@ORM\JoinColumn(name="columnC",referencedColumnName="columnD",unique=true)})
```

Documentation
-------------

- [Installing][]
- Usage
    - [Tokenizing][]
    - [Converting][]

[Doctrine annotations]: http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
[Annotations]: https://github.com/doctrine/annotations
[Installing]: docs/00-Installing.md
[Tokenizing]: docs/01-Tokenizing.md
[Converting]: docs/02-Converting.md
