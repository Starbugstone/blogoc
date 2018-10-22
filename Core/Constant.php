<?php

namespace Core;

/**
 * All our constants belong here
 *
 * PHP version 7
 */
class Constant
{
    /**
     * The admin and user levels
     * @var int
     */
    const ADMIN_LEVEL = 2;
    const USER_LEVEL = 1;

    /**
     * the number of pages to show
     */
    const FRONT_PAGE_POSTS = 3;
    const POSTS_PER_PAGE = 4;
    const LIST_PER_PAGE = 10;
    const COMMENTS_PER_PAGE = 2;

    const EXCERPT_WORD_COUNT =50;

    //login security
    const NUMBER_OF_BAD_PASSWORD_TRIES = 3;
    const LOCKOUT_MINUTES = 5;

    const PASSWORD_RESET_DURATION = 240;//number of minutes the reset password link is valid

    const HASH_KEY = "1337blogOcPass159758348ShaQpiss";
}