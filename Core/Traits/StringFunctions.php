<?php

namespace Core\Traits;

use Core\Constant;
use Twig\Error\Error;

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

    /**
     * create an excerpt, shortening the text to a specific number of words
     * @param string $text the text to shorten
     * @param int $count number of words
     * @return string the shortend text
     * @throws \ErrorException
     */
    public function getExcerpt(string $text, int $count = Constant::EXCERPT_WORD_COUNT)
    {
        if ($count < 1) {
            throw new \ErrorException('excerpt length too low');
        }

        $text = str_replace("  ", " ", $text);
        $string = explode(" ", $text);
        if (count($string) <= $count) {
            return $text;
        }
        $trimed = '';
        for ($wordCounter = 0; $wordCounter < $count; $wordCounter++) {
            //TODO Take into account the "read more" tag
            $trimed .= $string[$wordCounter];
            if ($wordCounter < $count - 1) {
                $trimed .= " ";
            } else {
                $trimed .= "...";
            }
        }
        $trimed = trim($trimed);
        return $trimed;
    }

    /**
     * check passed string, returns true if the string contains alphaNum or _ or -. use for checking database tables, column names or slugs
     * @param string $string the string to analyse
     * @return bool
     */
    public function isAlphaNum(string $string): bool
    {
        if (preg_match("/^[A-Za-z0-9_-]+$/", $string)) {
            return true;
        }
        return false;
    }
}