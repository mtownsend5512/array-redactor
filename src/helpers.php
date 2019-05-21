<?php

if (!function_exists('array_redactor')) {
    /**
     * Create a new instance of the ArrayRedactor class
     *
     * @param mixed array|string $content The content to redact (either array of json)
     * @param array $keys A non-associative array of keys that should be redacted in the content
     * @param mixed $ink What should replace the redacted data
     * @return object \Mtownsend\ArrayRedactor\ArrayRedactor
     */
    function array_redactor($content = [], array $keys = [], $ink = '[REDACTED]')
    {
        return new \Mtownsend\ArrayRedactor\ArrayRedactor($content, $keys, $ink);
    }
}
