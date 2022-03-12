<?php

namespace JSONSchemaGenerator\Structure;

use JSONSchemaGenerator\Mappers\StringMapper;

/**
 * Represents a Definition or Member as defined
 *
 * @link   http://tools.ietf.org/html/draft-zyp-json-schema-04#section-3.1
 * @link   http://tools.ietf.org/html/rfc4627
 * @author steven
 *
 */
class Definition implements \JsonSerializable
{
  public const ITEMS_AS_COLLECTION = 0; // use anyOf instead of strict collection
  public const ITEMS_AS_LIST = 1;

  /**
   * link to the resource identifier
   *
   * @var string|null $id
   */
  protected ?string $id = null;

  /**
   * @var string $type
   */
  protected string $type;

  /**
   *
   * @var string|null
   */
  protected ?string $title = null;

  /**
   *
   * @var string|null
   */
  protected ?string $description = null;

  /**
   * needs to be allowed to be set as a default config setting
   *
   * @var boolean
   */
  protected bool $required = false;

  /**
   * When numeric it's integer min or max
   * When it's array it's min/max items
   *
   * @var integer
   */
  protected ?int $min = null;

  /**
   * When numeric it's integer min or max
   * When it's array it's min/max items
   *
   * @var integer
   */
  protected ?int $max = null;

  /**
   * @var string|null $format guessed format from string or null if none
   */
  protected ?string $format;

  /**
   * sub properties
   *
   * @var Definition[]
   */
  protected array $properties = [];


  /**
   * sub items
   *
   * @var Definition[]
   */
  protected array $items = [];


  /**
   * If defaultValue instanceof Undefined remove the field from the schema
   *
   * @var mixed default value
   */
  protected $defaultValue;


  /**
   * @var null|array
   */
  protected ?array $enum = null;

  /**
   * @var int items collection mode, convert a list of various schema using anyOf or strict positionnal list of schema
   */
  protected int $collectionMode = 0;

  /**
   * setup default values
   */
  public function __construct()
  {
    $this->defaultValue = new UndefinedValue();
  }


  /**
   * @return string the $id
   */
  public function getId(): ?string
  {
    return $this->id;
  }

  /**
   * @return string the $type from JSONSchemaGenerator\Mappers\StringMapper::*
   */
  public function getType(): string
  {
    return $this->type;
  }


  /**
   * @return null|string the $title
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * @return null|string the $description
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * @return boolean the $required
   */
  public function isRequired(): bool
  {
    return !!$this->required;
  }

  /**
   * @return int the $min
   */
  public function getMin(): ?int
  {
    return $this->min;
  }

  /**
   * @return int the $max
   */
  public function getMax(): ?int
  {
    return $this->max;
  }

  /**
   * @return Definition[] the $properties
   */
  public function getProperties(): array
  {
    return $this->properties;
  }

  /**
   * @return Definition[] the $items
   */
  public function getItems(): array
  {
    return $this->items;
  }

  /**
   * @param string $id
   *
   * @return self
   */
  public function setId(?string $id): self
  {
    $this->id = $id;

    return $this;
  }

  /**
   * @param string $type
   *
   * @return self
   */
  public function setType(string $type): self
  {
    $this->type = $type;

    return $this;
  }


  /**
   * @param string $title
   *
   * @return self
   */
  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  /**
   * @param string $description
   *
   * @return self
   */
  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @param boolean $required
   *
   * @return self
   */
  public function setRequired(bool $required = false): self
  {
    $this->required = (bool)$required;

    return $this;
  }

  /**
   * @param integer $min
   *
   * @return self
   */
  public function setMin(int $min): self
  {
    $this->min = $min;

    return $this;
  }

  /**
   * @param integer $max
   *
   * @return self
   */
  public function setMax(int $max): self
  {
    $this->max = $max;

    return $this;
  }

  /**
   * @param string $key
   * @param Definition $value
   *
   * @return self
   */
  public function setProperty(string $key, self $value): self
  {
    $this->properties[$key] = $value;

    return $this;
  }

  /**
   * @param Definition[] $properties
   */
  public function setItems(array $properties): void
  {
    $this->items = [];
    foreach ($properties as $p) {
      $this->addItem($p);
    }
  }

  /**
   * @param Definition $def
   *
   * @return $this
   */
  public function addItem(self $def): self
  {
    if ($this->getCollectionMode() === self::ITEMS_AS_COLLECTION) {
      $def->setId(null); // make schema anonymous
    }

    foreach ($this->items as $i) {
      if ($this->getCollectionMode() === self::ITEMS_AS_COLLECTION && $i->equals($def)) {
        // item schema type already in list
        return $this;
      }
    }

    $this->items[] = $def;
    return $this;
  }


  /**
   * @return string[] a list of required properties
   */
  public function getRequireds(): array
  {
    $requireds = [];
    foreach ($this->properties as $name => $p) {
      if ($p->isRequired()) {
        $requireds[] = $name;
      }
    }
    sort($requireds);
    return $requireds;
  }

  /**
   * @return mixed
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }

  /**
   * @param mixed $defaultValue
   *
   * @return Definition
   */
  public function setDefaultValue($defaultValue): self
  {
    $this->defaultValue = $defaultValue;

    return $this;
  }

  /**
   * @return array|null
   */
  public function getEnum(): ?array
  {
    return $this->enum;
  }

  /**
   * @param array|null $enum
   *
   * @return Definition
   */
  public function setEnum(?array $enum): self
  {
    $this->enum = $enum;

    return $this;
  }

  /**
   * @return array flattened fields
   */
  public function flatten()
  {
    // field object - to force the object type in json encode
    $fa = new \stdClass();

    if (!empty($this->getId())) {
      $fa->id = $this->getId();
    }

    $fa->type = $this->getType();

    if ($this->getTitle()) {
      $fa->title = $this->getTitle();
    }

    if ($this->getDescription()) {
      $fa->description = $this->getDescription();
    }

    if ($fa->type === StringMapper::INTEGER_TYPE ||
      $fa->type === StringMapper::NUMBER_TYPE
    ) {
      if (!empty($this->min)) {
        $fa->min = $this->getMin();
      }
      if (!empty($this->max)) {
        $fa->max = $this->getMax();
      }
    }

    if ($fa->type === StringMapper::STRING_TYPE
      && $this->getFormat()
    ) {
      $fa->format = $this->getFormat();
    }

    /*
     * If a default value had been set
     */
    if (!$this->defaultValue instanceof UndefinedValue) {
      $fa->default = $this->defaultValue;
    }

    if ($this->getType() === StringMapper::ARRAY_TYPE) {

      // add the items
      $items = [];
      foreach ($this->getItems() as $item) {
        $items[] = $item->flatten();
      }

      if ($this->getCollectionMode() === self::ITEMS_AS_LIST) {
        $fa->items = $items;
      } else {
        // collection of various schema using 'anyOf'
        $fa->items = new \StdClass();
        // deduplicate items in anyOf type
        $fa->items->anyOf = $items;
      }

    } elseif ($this->getType() === StringMapper::OBJECT_TYPE) {
      if ($this->getRequireds()) {
        $fa->required = $this->getRequireds();
      }

      if ($this->getProperties()) {
        $fa->properties = new \StdClass();
        foreach ($this->getProperties() as $key => $property) {
          $fa->properties->$key = $property->flatten();
        }
      }
    }

    return $fa;
  }

  /**
   * @return int
   */
  public function getCollectionMode(): int
  {
    return $this->collectionMode;
  }

  /**
   * @param int $collectionMode
   *
   * @return Definition
   */
  public function setCollectionMode(int $collectionMode): self
  {
    $this->collectionMode = $collectionMode;

    return $this;
  }

  /**
   * @return null|string
   */
  public function getFormat(): ?string
  {
    return $this->format;
  }

  /**
   * @param null|string $format
   */
  public function setFormat($format): void
  {
    $this->format = $format;
  }


  /**
   * @return \stdClass
   */
  #[\ReturnTypeWillChange]
  public function jsonSerialize()
  {
    return $this->flatten();
  }


  /**
   * @param Definition $d
   *
   * @return bool
   */
  public function equals(Definition $d): bool
  {
    $one = json_decode(json_encode($d), true);
    $two = json_decode(json_encode($this), true);

    $this->sortJsonArray($one);
    $this->sortJsonArray($two);

    return json_encode($one) === json_encode($two);
  }

  /**
   * Recursively key sorting for json comparison
   *
   * @param $arr
   *
   * @return mixed
   */
  protected function sortJsonArray(&$arr)
  {
    foreach ($arr as &$value) {
      if (is_array($value)) {
        $this->sortJsonArray($value);
      }
    }
    ksort($arr);
    return $arr;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string)json_encode($this);
  }


}
