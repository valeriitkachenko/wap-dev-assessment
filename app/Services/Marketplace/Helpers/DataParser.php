<?php

namespace App\Services\Marketplace\Helpers;

use App\Services\Marketplace\Exceptions\ResponseParsingException;
use Exception;

class DataParser
{
    const FIELD_ENCODING_JPEG = 'base64;jpeg';
    const FIELD_ENCODING_PNG = 'base64;png';
    const FIELD_ENCODING_GIF = 'base64;gif';

    const CUSTOM_ENCODINGS = [
        self::FIELD_ENCODING_JPEG,
        self::FIELD_ENCODING_PNG,
        self::FIELD_ENCODING_GIF,
    ];

    const BASE64_MEDIA_TYPES = [
        self::FIELD_ENCODING_JPEG => 'image/jpeg',
        self::FIELD_ENCODING_PNG => 'image/png',
        self::FIELD_ENCODING_GIF => 'image/gif'
    ];

    /**
     * Parses custom encoded strings from Marketplace API
     *
     * @param string $value
     * @return array
     * @throws ResponseParsingException
     */
    public static function parse(string $value): array
    {
        $entityTypeAndEncodedProperties = self::getEntityTypeAndEncodedProperties($value);
        $entityType = $entityTypeAndEncodedProperties['type'];
        $encodedProperties = $entityTypeAndEncodedProperties['encodedProperties'];

        $decodedProperties = self::decodeEntityProperties($encodedProperties);

        return ['type' => $entityType, 'properties' => $decodedProperties];
    }

    /**
     * @param string $value
     * @return array
     * @throws Exception
     */
    private static function getEntityTypeAndEncodedProperties(string $value): array
    {
        preg_match('/(order|product):([^"]+)/', $value, $matches);

        if (empty($matches[1]) || empty($matches[2])) {
            throw new ResponseParsingException();
        }

        return ['type' => $matches[1], 'encodedProperties' => $matches[2]];
    }

    /**
     * @param string $encodedProperties
     * @return array
     * @throws ResponseParsingException
     */
    private static function decodeEntityProperties(string $encodedProperties): array
    {
        preg_match_all('/([a-zA-Z_]+)([\\\\]{2}([a-zA-Z0-9;]+))?{([^|}]+)}/', $encodedProperties, $matches);

        if (empty($matches[1]) || empty($matches[4])) {
            throw new ResponseParsingException();
        }

        $keys = $matches[1];
        $values = self::processDecodedValues($matches[4], $matches[3]);

        return array_combine($keys, $values);
    }

    /**
     * @param array $values
     * @param array $types
     * @return array
     */
    private static function processDecodedValues(array $values, array $types): array
    {
        $processedValues = [];

        foreach ($values as $key => $value) {
            $customType = $types[$key];

            $value = self::processRegularType($value);
            if (!empty($customType) && in_array($customType, self::CUSTOM_ENCODINGS)) {
                $value = self::processCustomType($value, $customType);
            }

            $processedValues[$key] = $value;
        }

        return $processedValues;
    }

    /**
     * @param $value
     * @return string
     */
    private static function processRegularType($value)
    {
        return stripslashes($value);
    }

    /**
     * @param string $value
     * @param string $type
     * @return string
     */
    private static function processCustomType(string $value, string $type): string
    {
        return sprintf('data:%s;base64,%s', self::BASE64_MEDIA_TYPES[$type], $value);
    }
}
