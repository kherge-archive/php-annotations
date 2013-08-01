Annotations
===========

[![Build Status][]](https://travis-ci.org/herrera-io/php-annotations)

Annotations is a generalized version of the [Doctrine Annoations][] library.
It is designed to work with Doctrine style annotations, but without requiring
that annotation classes or constants to exist. The library provides a way to
tokenize annotations and convert them to other formats.

> The `DocLexer` class from the Doctrine Annotations library is used.

Example
-------

```php
use Herrera\Annotations\Tokenizer;

$tokenizer = new Tokenizer();
$tokens = $tokenizer->parse(
    <<<DOCBLOCK
/**
 * @My\Annotation(
 *     a="string value",
 *     @Nested,
 *     {"a list"}
 * )
 */
DOCBLOCK
);

/*
 * array(
 *     array(DocLexer::T_AT),
 *     array(DocLexer::T_IDENTIFIER, 'My\\Annotation'),
 *     array(DocLexer::T_OPEN_PARENTHESIS),
 *     array(DocLexer::T_IDENTIFIER, 'a'),
 *     array(DocLexer::T_EQUALS),
 *     array(DocLexer::T_STRING, 'string value'),
 *     array(DocLexer::T_COMMA),
 *     array(DocLexer::T_AT),
 *     array(DocLexer::T_IDENTIFIER, 'Nested'),
 *     array(DocLexer::T_OPEN_CURLY_BRACES),
 *     array(DocLexer::T_STRING, 'a list'),
 *     array(DocLexer::T_CLOSE_CURLY_BRACES),
 *     array(DocLexer::T_CLOSE_PARENTHESIS)
 * )
 */
```

Installation
------------

Add it as a [Composer][] dependency:

```
$ composer require herrera-io/annotations=~1.0
```

Tokenizing
----------

To tokenize a docblock comment, you will first need to create an instance of
`Tokenizer`. The object can be re-used to parse as many docblocks as needed:

```php
use Herrera\Annotaitons\Tokenizer;

$tokenizer = new Tokenizer();

$parsed = $tokenizer->parse($docblock);
```

The value of `$parsed` is an array of arrays. Each array in `$parsed` will
contain a minimum of one value, and a maximum of two. The first value is the
token's numeric identifier, and the second is the value that came with the
token.

> You can find a references of the [token identfiers here][].

This example docblock:

```php
/**
 * @ORM\Column(name="MyColumn")
 */
```

will yield the following tokens in `$tokens`:

```php
$parsed = array(
    array(DocLexer::T_AT),
    array(DocLexer::T_IDENTIFIER, 'ORM\\Column'),
    array(DocLexer::T_OPEN_PARENTHESIS),
    array(DocLexer::T_IDENTIFIER, 'name'),
    array(DocLexer::T_EQUALS),
    array(DocLexer::T_STRING, 'MyColumn'),
    array(DocLexer::T_CLOSE_PARENTHESIS)
);
```

Converting
----------

Once you have parsed a docblock for its tokens, you may find the need to convert
the list of tokens into another format. Before I cover the available coverters,
I need to show you how to create an instance of `Tokens` and `Sequences` which
are consumed by the converters.

### Tokens and Sequences

Converters use either the `Tokens` or `Sequences` class when converting a list
of tokens into an alternative format. The `Tokens` class acts like an array,
but it will also validate the tokens as they are being used. The `Sequences`
class is an extension of `Tokens`, but it also validates the order in which
the tokens are used.

The converters only require that you use `Tokens`, but they are compatible
with the `Sequences` class as well. The only time you may find need for the
`Sequences` class is for debugging annotation issues, or if you are accepting
tokens from anything beside the `Tokenizer` class.

Creating an instance of either class is very simple:

```php
use Herrera\Annotations\Sequence;
use Herrera\Annotations\Tokens;

$tokens = new Tokens($parsed);
$sequence = new Sequence($parsed);
$sequence = new Sequence($tokens); // also accepts Tokens object
```

### To Array

To convert a list of tokens to an array, you will need to use an instance of
the `ToArray` class:

```php
use Herrera\Annotations\Convert\ToArray;

$toArray = new ToArray();

$array = $toArray->convert($tokens);
```

The resulting array that is returned is actually an array of objects. Each
object represents a single annotation. An annotation object will only ever
have two fields: `name` and `values`.

The following example:

```php
$array = $toArray->convert(
    $tokenizer->parse(
        <<<DOCBLOCK
/**
 * @Annotation\A("Just a simple value.")
 * @Annotation\B(
 *     name="SomeName",
 *     nested=@Annotation(),
 *     {
 *         "an array",
 *         {
 *             "within an array"
 *         }
 *     }
 * )
 */
DOCBLOCK
    )
);
```

will result with the following array:

```php
$array = array(
    (object) array(
        'name' => 'Annotation\\A',
        'values' => array(
            'Just a simple value.'
        )
    ),
    (object) array(
        'name' => 'Annotations\\B',
        'values' => array(
            'name' => 'SomeName',
            'nested' => (object) array(
                'name' => 'Annotation',
                'values' => array()
            ),
            array(
                'an array',
                array(
                    'within an array'
                )
            )
        )
    ),
);
```

### To String

To convert a list of tokens to a string, you will need to use an instance of
the `ToString` class:

```php
use Herrera\Annotations\Convert\ToString;

$toString = new ToString();

$string = $toString->convert($tokens);
```

Using this example:

```php
$string = $toString->convert(
    $tokenizer->parse(
        <<<DOCBLOCK
/**
 * @Annotation\A("Just a simple value.")
 * @Annotation\B(
 *     name="SomeName",
 *     nested=@Annotation(),
 *     {
 *         "an array",
 *         {
 *             "within an array"
 *         }
 *     }
 * )
 */
DOCBLOCK
    )
);

The result will be similar, but without any of the formatting:

```php
$string = '@Annotation\A("Just a simple value.")@Annotation\B(name="SomeName",nested=@Annotation(),{"an array",{"within an array"}})';
```

While formatting is supported by the string converter, it is very limited in
the number of options it provides:

- `setBreakChar($char)` &mdash; Sets the line break character. (default: `\n`)
- `setIndentChar($char)` &mdash; Sets the indentation character. (default: ` ` (space))
- `setIndentSize($size)` &mdash; Sets the indentation size. (default: `0` (zero))
- `useColonSpace($bool)` &mdash; Toggles whether a space should be added after a colon that is used for assignment. (default: `false`) (example: `@Name({key: "value"})`)

With a minor modification:

```php
$toString->setIndentSize(4);
```

We can get a formatted string returned back to us:

```php
$string = <<<STRUNG
@Annotation\A(
    "Just a simple value."
)
@Annotation\B(
    name="SomeName",
    nested=@Annotation(),
    {
        "an array",
        {
            "within an array"
        }
    }
)
STRUNG;
```

[Build Status]: https://travis-ci.org/herrera-io/php-annotations.png?branch=master
[Doctrine annotations]: http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
[Composer]: http://getcomposer.org/
[token identifiers here]: https://github.com/doctrine/annotations/blob/master/lib/Doctrine/Common/Annotations/DocLexer.php#L35
