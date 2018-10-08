<?php

namespace Core\Traits;

use Core\Constant;

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

        //Searching for the page break tag
        $breakTagPosition = strpos($text, "<!-- EndOfExcerptBlogOc -->");
        if($breakTagPosition > 0){
            return $this->completeDom(substr($text, 0, $breakTagPosition));
        }

        //exploding on space except for in img, p and span.
        $string = preg_split('/(<img[^>]+\>)|(<p[^>]+\>)|(<span[^>]+\>)|\s/', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        //The preg split can return false, probably will never happen but just in case.
        if(!$string)
        {
            throw new \Error("excerpt generation failed");
        }

        if (count($string) <= $count) {
            return $text;
        }
        $trimed = '';
        for ($wordCounter = 0; $wordCounter < $count; $wordCounter++) {
            $trimed .= $string[$wordCounter];
            if ($wordCounter < $count - 1) {
                $trimed .= " ";
            } else {
                $trimed .= "[...]";
            }
        }

        return $this->completeDom($trimed);
    }

    /**
     * Close the dom in given $text
     * @param string $text unclean html
     * @return string cleaned up html
     */
    private function completeDom(string $text): string
    {
        //grabbed from https://gist.github.com/JayWood/348752b568ecd63ae5ce#gistcomment-2310550
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument;
        $dom->loadHTML($text);

        // Strip wrapping <html> and <body> tags
        $mock = new \DOMDocument;
        $body = $dom->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $child) {
            $mock->appendChild($mock->importNode($child, true));
        }

        return trim($mock->saveHTML());
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

    /**
     * check if each string in passed array is valid
     * @param array $strings
     * @return bool
     */
    public function isAllAlphaNum(array $strings):bool
    {
        $result = true;
        foreach ($strings as $string)
        {
            if(!$this->isAlphaNum($string))
            {
                $result = false;
            }
        }
        return $result;
    }
}