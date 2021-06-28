<?php

namespace JSONSchemaGenerator\Mappers;

/**
 *
 * @package JSONSchemaGenerator\Mappers
 * @author  steven
 *
 */
class StringMapper extends PropertyTypeMapper
{
  public const MODE_REGEX = 'Regex';
  public const MODE_PHP_NATIVE_FILTER = "Filter";

  // @codingStandardsIgnoreStart
  // @see https://www.w3.org/TR/2012/REC-xmlschema11-2-20120405/datatypes.html#dateTime-lexical-mapping
  public const DATE_TIME_PATTERN = '/^-?([1-9][0-9]{3,}|0[0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])T(([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9](\.[0-9]+)?|(24:00:00(\.0+)?))(Z|(\+|-)((0[0-9]|1[0-3]):[0-5][0-9]|14:00))?$/';
  // @codingStandardsIgnoreEnd

  public const HOST_NAME_PATTERN = '/^[_a-z]+\.([_a-z]+\.?)+$/i';

  public const FORMAT_DATE_TIME = "date-time";
  public const FORMAT_URI = "uri";
  public const FORMAT_EMAIL = "email";
  public const FORMAT_IPV4 = "ipv4";
  public const FORMAT_IPV6 = "ipv6";
  public const FORMAT_HOSTNAME = "hostname";

  /**
   * @return string[] a list of format name self::FORMAT_*
   */
  protected static function getValidationTests(): array
  {
    return [
      self::FORMAT_DATE_TIME,
      self::FORMAT_URI,
      self::FORMAT_EMAIL,
      self::FORMAT_IPV4,
      self::FORMAT_IPV6,
      self::FORMAT_HOSTNAME,
    ];
  }

  /**
   * @param string $string
   *
   * @return null|string string format or null if none found
   */
  public static function guessStringFormat($string): ?string
  {
    foreach (self::getValidationTests() as $formatName) {
      if (self::validate($string, $formatName)) {
        return $formatName;
      }
    }
    return null;
  }

  /**
   * @param string $value given value to test
   * @param string $testName test format case self::FORMAT_*
   *
   * @return bool
   */
  public static function validate($value, $testName): bool
  {
    switch ($testName) {
      case 'date-time':
        return self::validateRegex(
          $value,
          self::DATE_TIME_PATTERN
        );
      case 'uri':
        return self::validateFilter(
          $value,
          FILTER_VALIDATE_URL,
          null
        );
      case 'email':
        return self::validateFilter(
          $value,
          FILTER_VALIDATE_EMAIL,
          null
        );
      case 'ipv4':
        return self::validateFilter(
          $value,
          FILTER_VALIDATE_IP,
          FILTER_FLAG_IPV4
        );
      case 'ipv6':
        return self::validateFilter(
          $value,
          FILTER_VALIDATE_IP,
          FILTER_FLAG_IPV6
        );
      case 'hostname':
        return self::validateRegex(
          $value,
          self::HOST_NAME_PATTERN
        );
    }

    return false;
  }

  /**
   * @param mixed $value
   * @param string $pattern
   *
   * @return bool
   */
  protected static function validateRegex($value, $pattern): bool
  {
    return (!is_string($value) || preg_match($pattern, $value) === 1);
  }

  /**
   * @param mixed $value
   * @param int $filter
   * @param mixed $options
   *
   * @return bool true if ok, false otherwise
   *
   */
  protected static function validateFilter($value, int $filter, $options): bool
  {
    if (!is_string($value) || filter_var($value, $filter, $options) !== false) {
      return true;
    }

    // This workaround allows otherwise valid protocol relative urls to pass.
    // @see https://bugs.php.net/bug.php?id=72301
    return $filter === FILTER_VALIDATE_URL
      && strpos($value, '//') === 0
      && filter_var('http:' . $value, $filter, $options) !== false;
  }
}
