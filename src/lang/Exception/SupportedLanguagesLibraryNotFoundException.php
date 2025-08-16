<?php

namespace Simp\Translate\lang\Exception;

class SupportedLanguagesLibraryNotFoundException extends \Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}