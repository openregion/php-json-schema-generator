<?php

namespace JSONSchemaGenerator\Structure;

/**
 * A JSON document
 * Represents the body of the schema
 * Can be decomposed and consolidated as a simple array of key values for easy json_encoding
 *
 * @link http://tools.ietf.org/html/draft-zyp-json-schema-04#section-3.2
 * @author steven
 * @package JSONSchemaGenerator\Structure
 */
class Schema extends Definition
{
  /**
   * @var string
   */
  protected string $dollarSchema = 'http://json-schema.org/draft-04/schema#';

  /**
   * the ID is a string reference to the resource that is identified in this document
   * As this JSON document is defined the base URL should be provided and set otherwise
   * the json schema
   *
   * @var string|null $id
   */
  protected ?string $id = null;

  /**
   * the JSON primitive type
   * Default MUST be object type
   * Section 3.2
   *
   * @var string $type
   */
  protected string $type = 'object';

  /**
   * type of media content
   *
   * @var string
   */
  protected string $mediaType = 'application/schema+json';

  /**
   * during the return it can clean up empty fields
   *
   * @var boolean
   */
  protected bool $pruneEmptyFields = true;


  /**
   * A string representation of this Schema
   *
   * @return string
   */
  public function toString(): string
  {
    return (string)json_encode($this);
  }

  /**
   * Main schema generation utility
   *
   * @return array list of fields and values including the properties/items
   */
  public function flatten()
  {
    $def = new \stdClass();
    $def->{'$schema'} = $this->dollarSchema;
    foreach (parent::flatten() as $k => $v) {
      $def->$k = $v;
    }

    return $def;
  }

  /**
   * @return string
   */
  public function getDollarSchema(): string
  {
    return $this->dollarSchema;
  }

  /**
   * @param string $dollarSchema
   */
  public function setDollarSchema($dollarSchema): void
  {
    $this->dollarSchema = $dollarSchema;
  }
}
