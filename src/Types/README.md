# About the php-cypher-dsl type structure

Each type in Cypher (see https://neo4j.com/docs/cypher-manual/current/syntax/values/) is represented by a `Type` 
interface and corresponding trait. These types are combined into a hierarchy, like so:

```
└ AnyType
  └ StructuralType
    └ NodeType
    └ PathType
    └ RelationshipType
  └ PropertyType
    └ BooleanType
    └ DateTimeType
    └ DateType
    └ LocalDateTimeType
    └ LocalTimeType
    └ NumeralType
    └ PointType
    └ StringType
    └ TimeType
  └ CompositeType
    └ ListType
    └ MapType
```

These interfaces contain the signatures of the functions that are part of the builder pattern. For instance, `and`, 
`or`, `xor` and `not` can be called on any object that implements the `BooleanType` interface. Also, since `BooleanType`
extends `PropertyType`, all functions defined in there also have to be implemented. To prevent having to implement all 
these functions for each class separately, each type interface is accompanied by a corresponding trait which provides a 
default implementation for the functions.

These type interfaces are also used to type hint input parameters of functions.

## The `Methods` namespace

This trait-based type system has one limitation: it cannot handle methods that need to be implemented by more than one 
independent type. This is because a fatal error is produced if two traits insert a method with the same name:

```plain
Trait method NodeTypeTrait::property has not been applied as Variable::property, because of collision with MapTypeTrait::property 
```

To solve this, we extract the colliding method out into a separate trait, which is then used by all traits that need
to implement that function. This works, because methods originating from the same sub-trait are not considered as 
conflicting (see https://bugs.php.net/bug.php?id=63911).

To guarantee the function definition stays the same across the types, we do the same for the interface.
