Converting
==========

The `Convert` class is used for converting tokens to another format.

```php
use Herrera\Annotations\Convert;
```

To convert an array of tokens back to a string, call the `toString()` method:

(using example from `01-Tokenizing.md`)

```php
$string = Convert::toString($tokens);

// @ORM\JoinTable(name="myJoinTable",joinColumns={@ORM\JoinColumn(name="columnA",referencedColumnName="columnB")},inverseJoinColumns={@ORM\JoinColumn(name="columnC",referencedColumnName="columnD",unique=true)})
```
