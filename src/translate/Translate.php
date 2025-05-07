<?php

namespace Simp\Translate\translate;

use Simp\Translate\lang\LanguageManager;
use Statickidz\GoogleTranslate;
use Symfony\Component\Yaml\Yaml;

class Translate
{

    protected ?string $cacheLocation;
    /**
     * The original text content.
     */
    protected string $originalText;

    /**
     * The translated text content.
     */
    protected string $translatedText;

    /**
     * The language of the original text.
     */
    protected array $originalLanguage;

    /**
     * The language to which the text is translated.
     */
    protected array $translatedLanguage;

    /**
     * The translated text content.
     */
    protected GoogleTranslate $translation;

    /**
     * Constructor method for initializing translation attributes.
     *
     * @param array $originalLanguage The source language metadata.
     * @param array $translatedLanguage The target language metadata.
     * @param string $originalText The original text to be translated (optional).
     * @param string|null $cacheLocation
     */
    public function __construct(array $originalLanguage, array $translatedLanguage, string $originalText = '', ?string $cacheLocation = null)
    {
        $this->originalText = $originalText;
        $this->translatedText = '';
        $this->originalLanguage = $originalLanguage;
        $this->translatedLanguage = $translatedLanguage;
        $this->cacheLocation = $cacheLocation;
        $this->translation = new GoogleTranslate();
    }


    /**
     * Translates the original text from the source language to the target language.
     *
     * @return string The translated text.
     */
    public function process(): string
    {

        $file_name = $this->originalLanguage['code'] . '-' . $this->translatedLanguage['code'] . '.yml';
        $key = $this->sanitize_with_dots($this->originalText);

        if (!empty($key)) {
            $path = $this->cacheLocation . '/' . $file_name;
            if (file_exists($path)) {
                $yaml = Yaml::parseFile($path);
                if (isset($yaml[$key])) {
                    $this->translatedText = $yaml[$key];
                    return $yaml[$key];
                }
            }
        }

        $this->translatedText = $this->translation->translate($this->originalLanguage['code'], $this->translatedLanguage['code'], $this->originalText);
        if ($this->cacheLocation) {
            if (!is_dir($this->cacheLocation)) {
                @mkdir($this->cacheLocation, 0777, true);
            }
            if (!file_exists($this->cacheLocation . '/' . $file_name)) {
                @touch($this->cacheLocation . '/' . $file_name);
            }
            $old_yaml = Yaml::parseFile($this->cacheLocation . '/' . $file_name) ?? [];
            file_put_contents($this->cacheLocation . '/' . $file_name, Yaml::dump([...$old_yaml, $key => $this->translatedText]), Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        }
        return $this->translatedText;
    }

    protected function sanitize_with_dots($string): array|string|null
    {
        // Replace any non-alphanumeric character (including space) with a dot.
        return preg_replace('/[^a-zA-Z0-9]/', '.', $string);
    }

    /**
     * Retrieves the translated text.
     *
     * @return string The translated text.
     */
    public function getTranslatedText(): string
    {
        return $this->translatedText;
    }

    /**
     * Retrieves the original text.
     *
     * @return string The original text.
     */
    public function getOriginalText(): string
    {
        return $this->originalText;
    }

    /**
     * Retrieves the original language(s) associated with the context or data.
     *
     * @return array The original language(s) as an array.
     */
    public function getOriginalLanguage(): array
    {
        return $this->originalLanguage;
    }

    /**
     * Retrieves the translated language(s) associated with the context or data.
     *
     * @return array The translated language(s) as an array.
     */
    public function getTranslatedLanguage(): array
    {
        return $this->translatedLanguage;
    }

    /**
     * Retrieves the translation object associated with the context or data.
     *
     * @return GoogleTranslate The translation object.
     */
    public function getTranslation(): GoogleTranslate
    {
        return $this->translation;
    }

    /**
     * Provides the translation object representing the current instance.
     *
     * @return static The current instance of the translation object.
     */
    public function translationObject(): static
    {
        return $this;
    }

    /**
     * Translates the given text from the source language to the target language.
     *
     * @param string $text The text to be translated.
     * @param string $from The source language code. Defaults to 'en'.
     * @param string $to The target language code. Defaults to 'fr'.
     * @param string|null $cacheLocation Optional cache location for storing translation data.
     * @return string The translated text.
     */
    public static function t(string $text, string $from = 'en', string $to = 'fr', ?string $cacheLocation = null): string
    {
        $self = new self(
            LanguageManager::manager()->getByCode($from),
            LanguageManager::manager()->getByCode($to),
            $text,
            $cacheLocation
        );
        return $self->process();
    }

    /**
     * Translates the given text from one language to another.
     *
     * @param string $text The text to be translated.
     * @param string $from The source language code (default is 'en').
     * @param string $to The target language code (default is 'fr').
     * @param string|null $cacheLocation Optional location for caching the translation.
     *
     * @return Translate An instance containing the translated content and related information.
     */
    public static function translate(string $text, string $from = 'en', string $to = 'fr', ?string $cacheLocation = null): Translate
    {
        return new self(
            LanguageManager::manager()->getByCode($from),
            LanguageManager::manager()->getByCode($to),
            $text,
            $cacheLocation
        );
    }

    public function __toString(): string
    {
        $this->process();
        return $this->getTranslatedText();
    }
}