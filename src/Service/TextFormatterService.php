<?php

namespace App\Service;

class TextFormatter
{

    public function formatSlug(string $text): string
    {
        $removeSpaces = str_replace(' ', '', $text);
        $lowercase = strtolower($removeSpaces);
        $threeLetters = substr($lowercase, 0, 4);

        return $threeLetters;
    }

}
