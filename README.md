# php-cypher-dsl

The `php-cypher-dsl` library provides a way to construct advanced Cypher
queries in an object-oriented and type-safe manner.

## Documentation

[The documentation can be found on the wiki
here.](https://github.com/WikibaseSolutions/php-cypher-dsl/wiki)

## Installation

### Requirements

`php-cypher-dsl` requires PHP 7.4 or greater; using the latest version of PHP
is highly recommended.

### Installation through Composer

You can install `php-cypher-dsl` through composer by running the following
command:

```
composer require "wikibase-solutions/php-cypher-dsl"
```

## Contributing

Please refer to [CONTRIBUTING.md](https://github.com/neo4j-php/php-cypher-dsl/blob/main/.github/CONTRIBUTING.md) for information on how to contribute to this project.

## Example

To construct a query to find all of Tom Hanks' co-actors, you can use the
following code:

```php
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;

$tom = node("Person")->withProperties(["name" => "Tom Hanks"]);
$coActors = node();

$statement = query()
    ->match($tom->relationshipTo(Query::node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
    ->returning($coActors->property("name"))
    ->build();
```
