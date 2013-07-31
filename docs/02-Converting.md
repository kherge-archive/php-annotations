Converting
==========

The library includes two conversion classes for tokens.

- `Herrera\Annotations\Convert\TokensToArray`
- `Herrera\Annotations\Convert\TokensToString`

> The examples below reference the `$tokens` variable created in the example
> from `01-Tokenizing.md`.

TokensToArray
-------------

Using the following exmaple:

```php
use Herrera\Annotations\Convert\TokensToArray;

$converter = new TokensToArray;

print_r($converter->convert($tokens);
```

will result in the following output:

```
Array
(
    [0] => stdClass Object
        (
            [name] => ORM\JoinTable
            [values] => Array
                (
                    [name] => myJoinTable
                    [joinColumns] => Array
                        (
                            [0] => stdClass Object
                                (
                                    [name] => ORM\JoinColumn
                                    [values] => Array
                                        (
                                            [name] => columnA
                                            [referencedColumnName] => columnB
                                        )

                                )

                        )

                    [inverseJoinColumns] => Array
                        (
                            [0] => stdClass Object
                                (
                                    [name] => ORM\JoinColumn
                                    [values] => Array
                                        (
                                            [name] => columnC
                                            [referencedColumnName] => columnD
                                            [unique] => 1
                                        )

                                )

                        )

                )

        )

)
```

TokensToString
--------------

Using the following example:

```php
use Herrera\Annotations\Convert\TokensToString;

$converter = new TokensToString();

echo $converter->convert($tokens);
```

will result in the following output:

```
@ORM\JoinTable(name="myJoinTable",joinColumns={@ORM\JoinColumn(name="columnA",referencedColumnName="columnB")},inverseJoinColumns={@ORM\JoinColumn(name="columnC",referencedColumnName="columnD",unique=true)})
```

You also have some options in adjusting the formatting:

- `setBreakChar($char)` &mdash; Sets the line break character used.
  (default: "\n")
- `setIndentChar($char)` &mdash; Sets the indentation character used.
  (default: " ")
- `setIndentSize($size)` &mdash; Sets the number of times to repeat the indent
  character. (default: 0)
- `useColonSpace($bool)` &mdash; Toggles whether a space should be added after
  using a colon (`:`) as an assignment operator.

Setting the indentation size to anything greater than zero will trigger
indentation formatting.

```php
$converter->setIndentSize(4);
```

will result in this:

```
@ORM\JoinTable(
    name="myJoinTable",
    joinColumns={
        @ORM\JoinColumn(
            name="columnA",
            referencedColumnName="columnB"
        )
    },
    inverseJoinColumns={
        @ORM\JoinColumn(
            name="columnC",
            referencedColumnName="columnD",
            unique=true
        )
    }
)
```
