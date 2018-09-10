<?php

namespace Core\Traits;

/**
 * a trait with some string related helpers
 * Trait StringFunctions
 * @package Core\Traits
 */
trait StringFunctions
{
    /**
     * does a haystack start with a needle ?
     * @param $haystack string the string to search in
     * @param $needle string the string to search for
     * @return bool
     */
    public function startsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Does a haystack end with a needle
     * @param $haystack string the string to search in
     * @param $needle string the string to search for
     * @return bool
     */
    public function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Remove the tail of a string
     * @param $string string to slice apart
     * @param $tail string the string to remove
     * @return string
     */
    public function removeFromEnd(string $string, string $tail): string
    {
        if ($this->endsWith($string, $tail)) {
            $string = substr($string, 0, -strlen($tail));
        }
        return $string;
    }

    /**
     * remove some characters from the front of the string
     * @param string $string the string to be decapitated
     * @param string $head the head to be cut off ("OFF WITH HIS HEAD")
     * @return string
     */
    public function removeFromBeginning(string $string, string $head): string
    {
        if ($this->startsWith($string, $head)) {
            $string = substr($string, strlen($head));
        }
        return $string;
    }
}