<?php

namespace JSONSchemaGenerator;

use JSONSchemaGenerator\Parsers\BaseParser;

/**
 *
 * JSON Schema Generator
 *
 * Duties:
 * Take object arguments
 * Factory load appropriate parser
 *
 *
 * @package JSONSchema
 * @author  solvire
 *
 */
abstract class Generator
{
  /**
   * @param mixed $object
   * @param array|null $config
   *
   * @return string
   * @throws \JSONSchemaGenerator\Parsers\Exceptions\UnmappableException
   */
  public static function from($object, array $config = null): string
  {
    return (new BaseParser($config))->parse($object)->json();
  }

  /**
   * @param string $jsonString
   * @param array|null $config
   *
   * @return string
   * @throws \JSONSchemaGenerator\Parsers\Exceptions\UnmappableException
   */
  public static function fromJson(string $jsonString, array $config = null): string
  {
    return (new BaseParser($config))->parse(json_decode($jsonString))->json();
  }

}
