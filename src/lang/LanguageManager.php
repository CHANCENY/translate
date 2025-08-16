<?php

namespace Simp\Translate\lang;

use Simp\Translate\lang\Exception\SupportedLanguagesLibraryNotFoundException;

class LanguageManager
{
    protected array|null $languages = [];

    /**
     * @throws SupportedLanguagesLibraryNotFoundException
     */
    public function __construct()
    {
        if (file_exists(__DIR__ . '/languages.php')) {
            $this->languages = require __DIR__ . '/languages.php';
        } else {
            throw new SupportedLanguagesLibraryNotFoundException("Supported languages library not found. please run composer update");
        }
    }

    /**
     * Creates and returns a new instance of the LanguageManager.
     *
     * @return LanguageManager A new instance of LanguageManager.
     */
    public static function manager(): LanguageManager
    {
        return new self();
    }

    /**
     * Retrieves a language object based on a specified value and search criteria.
     *
     * @param string $value The value to search for within the languages.
     * @param string $by The criteria used for searching (default is 'code').
     * @return array|null The matched language object as an array, or null if no match is found.
     */
    public function getLanguageObject(string $value, string $by = 'code'): ?array
    {
        return $this->recursiveSearch($value, $this->languages, $by);
    }

    /**
     * Searches recursively through a multidimensional array of languages to find a language matching the specified value.
     *
     * @param string $value The value to search for within the array.
     * @param array $languages
     * @param string $by The key to search by within each language array. Defaults to 'code'.
     * @return array|null Returns the matching language array if found, or null if no match is found.
     */
    protected function recursiveSearch(string $value, array $languages, string $by = 'code'): ?array
    {
        foreach ($languages as $language) {
            if (is_array($language) && isset($language[$by]) && $language[$by] === $value) {
                return $language;
            } else if (is_array($language)) {
                $v = $this->recursiveSearch($value, $language, $by);
                if ($v) {
                    return $v;
                }
            }
        }
        return null;
    }

    /**
     * Retrieves a language object based on the provided code.
     *
     * @param string $code The code of the language to retrieve.
     * @return ?array The language object corresponding to the provided code.
     */
    public function getByCode(string $code): ?array
    {
        return $this->getLanguageObject($code, 'code');
    }

    /**
     * Retrieves a language object based on the provided title.
     *
     * @param string $title The title of the language to search for.
     * @return array|null The language object as an associative array, or null if not found.
     */
    public function getByTitle(string $title): ?array
    {
        return $this->getLanguageObject($title, 'language');

    }

    /**
     * Checks if the given code is supported.
     *
     * @param string $code The code to check for support.
     * @return bool Returns true if the code is supported, false otherwise.
     */
    public function isSupported(string $code): bool
    {
        return (bool)$this->getByCode($code);
    }
    
    public function getLanguages(): array {
        $all = [];
        foreach ($this->languages as $language) {
            forEach($language as $key=>$value) {
                if (is_array($value)) {
                    $all[$value['code']] = $value['language'];
                }
            }

        }
        return $all;
    }
    
}