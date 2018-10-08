<?php

namespace Core\Traits;

trait PasswordFunctions
{
    public function isPasswordComplex(string $password):array
    {
        $errors = array();
        $errors["success"] = true;
        $errors["message"] = null;

        if (strlen($password) < 8) {
            $errors["success"] = false;
            $errors["message"] = "Password too short!";
        }

        if (!preg_match("#[0-9]+#", $password)) {
            $errors["success"] = false;
            $errors["message"] = "Password must include at least one number!";
        }

        if (!preg_match("#[a-zA-Z]+#", $password)) {
            $errors["success"] = false;
            $errors["message"] = "Password must include at least one letter!";
        }

        return $errors;
    }
}