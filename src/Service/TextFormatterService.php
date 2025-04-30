<?php

namespace App\Service;

/**
 * This service provides helper functions to format text.
 */
class TextFormatterService
{

    /**
     * Create a short "slug" from a text.
     * - Removes spaces
     * - Converts to lowercase
     * - Returns the first 4 letters
     *
     * @param string $text The input text
     * @return string The formatted slug
    */
    public function formatSlug(string $text): string
    {
        $removeSpaces = str_replace(' ', '', $text);
        $lowercase = strtolower($removeSpaces);
        $threeLetters = substr($lowercase, 0, 4);
        return $threeLetters;
    }


    /**
     * Make the first letter uppercase and all others lowercase.
     *
     * @param string $text The input text
     * @return string The text with the first letter in uppercase
    */
    public static function formatUcFirst(string $text): string
    {
        return ucfirst(strtolower($text));
    }

    /**
     * Clean a description:
     * - Removes extra spaces around the text
     * - Capitalizes the first letter
     *
     * @param string $description The input description
     * @return string The formatted description
    */
    public function formatDescription(string $description): string
    {
        $trimmed = trim($description);
        $ucFirst = ucfirst($trimmed);
        return $ucFirst;
    }
}