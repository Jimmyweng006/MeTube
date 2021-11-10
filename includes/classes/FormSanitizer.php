<?php
Class FormSanitizer {

    public static function sanitizeFormString($inputString) {
        $inputString = strip_tags($inputString);
        $inputString = str_replace(" ", "", $inputString);
        $inputString = strtolower($inputString);
        $inputString = ucfirst($inputString);

        return $inputString;
    }

    public static function sanitizeFormUsername($inputString) {
        $inputString = strip_tags($inputString);
        $inputString = str_replace(" ", "", $inputString);

        return $inputString;
    }

    public static function sanitizeFormEmail($inputString) {
        $inputString = strip_tags($inputString);
        $inputString = str_replace(" ", "", $inputString);

        return $inputString;
    }

    public static function sanitizeFormPassword($inputString) {
        $inputString = strip_tags($inputString);

        return $inputString;
    }
}
?>