<?php

namespace Mtownsend\ArrayRedactor;

use Mtownsend\ArrayRedactor\Exceptions\ArrayRedactorException;

/**
 * @author Mark Townsend
 *
 */
class ArrayRedactor
{
    /**
     * The content to redact (either array of json)
     *
     * @var mixed array|string
     */
    public $content;

    /**
     * A non-associative array of keys that should be redacted in the content
     *
     * @var array
     */
    public $keys;

    /**
     * What should replace the redacted data
     *
     * @var mixed
     */
    public $ink;

    /**
     * Instantiate the ArrayRedactor class
     *
     * @param mixed array|string $content The content to redact (either array of json)
     * @param array $keys A non-associative array of keys that should be redacted in the content
     * @param mixed $ink What should replace the redacted data
     */
    public function __construct($content = [], array $keys = [], $ink = '[REDACTED]')
    {
        $this->content = $content;
        $this->keys = $keys;
        $this->ink = $ink;
    }

    /**
     * Provide the content to undergo redaction
     *
     * @param  mixed array|string $content The content to redact (either array of json)
     * @return object \Mtownsend\ArrayRedactor\ArrayRedactor
     */
    public function content($content = [])
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the keys to redact
     *
     * @param  array $keys A non-associative array of keys that should be redacted in the content
     * @return object \Mtownsend\ArrayRedactor\ArrayRedactor
     */
    public function keys($keys = [])
    {
        $this->keys = $keys;
        return $this;
    }

    /**
     * Set the value to replace redacted key values with
     *
     * @param  mixed $ink What should replace the redacted data
     * @return object \Mtownsend\ArrayRedactor\ArrayRedactor
     */
    public function ink($ink = '[REDACTED]')
    {
        $this->ink = is_callable($ink) ? $ink() : $ink;
        return $this;
    }

    /**
     * Apply recursive array redaction to the content
     *
     * @return array
     */
    public function redact()
    {
        if (is_string($this->content) && $this->isValidJson($this->content)) {
            $this->content = json_decode($this->content, true);
        }

        if (!is_array($this->content) || !$this->isAssocArray($this->content)) {
            throw new ArrayRedactorException("ArrayRedactor received invalid content `{$this->content}`");
        }

        // Recursively traverse the array and redact the specified keys
        array_walk_recursive($this->content, function (&$value, $key) {
            if (in_array($key, $this->keys, true)) {
                $value = is_callable($ink) ? $ink($value) : $this->ink;
            }
        });

        return $this->content;
    }

    /**
     * Return a json string
     *
     * @return string
     */
    public function redactToJson()
    {
        return json_encode($this->redact());
    }

    public function __toString()
    {
        return $this->redactToJson();
    }

    public function __invoke()
    {
        return $this->redact();
    }

    /**
     * Determine if the given array is associative or non-associative
     *
     * @param  array  $array
     * @return boolean
     */
    protected function isAssocArray(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * Check if the string received is valid json
     *
     * @param  string $string Assumed json string
     * @return boolean
     */
    protected function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
