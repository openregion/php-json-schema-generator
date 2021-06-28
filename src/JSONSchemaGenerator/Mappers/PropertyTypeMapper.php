<?php

namespace JSONSchemaGenerator\Mappers;

use JSONSchemaGenerator\Parsers\Exceptions\UnmappableException;

/**
 *
 * @package JSONSchemaGenerator\Mappers
 * @author steven
 *
 */
class PropertyTypeMapper
{
  // a little redundant but a nice key for hitting the arrays
  public const ARRAY_TYPE = 'array';
  public const BOOLEAN_TYPE = 'boolean';
  public const INTEGER_TYPE = 'integer';
  public const NUMBER_TYPE = 'number';
  public const NULL_TYPE = 'null';
  public const OBJECT_TYPE = 'object';
  public const STRING_TYPE = 'string';

  /**
   * @var string|null
   */
  protected ?string $property = null;

  /**
   * defines the primitive types
   *
   * @link http://tools.ietf.org/html/draft-zyp-json-schema-04#section-3.5
   * @var array
   */
  protected array $primitiveTypes = [
    'array' => ['description' => 'A JSON array'],
    'boolean' => ['description' => 'A JSON boolean'],
    'integer' => ['description' => 'A JSON number without a fraction or exponent part'],
    'number' => ['description' => 'A JSON number.  Number includes integer.'],
    'null' => ['description' => 'A JSON null value'],
    'object' => ['description' => 'A JSON object'],
    'string' => ['description' => 'A JSON string'],
  ];

  /**
   *
   * @param string $property
   */
  public function __construct(string $property)
  {
    $this->property = $property;
  }


  /**
   * the goal here would be go into a logic tree and work
   * from loosest definition to most strict
   *
   * @param mixed $property
   *
   * @return string
   * @throws UnmappableException
   */
  public static function map($property): string
  {
    // need to find a better way to determine what the string is
    switch (strtolower(gettype($property))) {
      case "double":
      case "float":
        return self::NUMBER_TYPE;
      case 'integer':
        return self::INTEGER_TYPE;
      case 'boolean':
        return self::BOOLEAN_TYPE;
      case 'array':
        if (array_values($property) !== $property) { // hash values
          return self::OBJECT_TYPE;
        }

        return self::ARRAY_TYPE;
      case 'null':
        return self::NULL_TYPE;
      case 'object':
        return self::OBJECT_TYPE;
      case 'string':
        return self::STRING_TYPE;
      default:
        throw new UnmappableException("The provided argument property");
    }
  }

  public function setProperty($property): self
  {
    $this->property = $property;
    return $this;
  }

  public function getProperty()
  {
    return $this->property();
  }

  public function getPropertyType(): void
  {

  }
}
