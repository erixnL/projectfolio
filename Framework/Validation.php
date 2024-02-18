<?php

namespace Framework;

class Validation {
    /**
     * Validate a string
     * 
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    //static method don't need to be instantiated, can be used directly with the class
     public static function string($value, $min = 1, $max = INF) {
        if (is_string($value)) {
            $value = trim($value);
            $length = strlen($value);
            //cannot be empty
            return $length >= $min && $length <= $max;
        }
        return false;
     }

     /**
      * Validate email address
      * 
      * @param string $value
      * @return mixed (false if not a email, if a validate email ,return that email)
      */
      public static function email ($value) {
        $value = trim($value);

        return filter_var($value, FILTER_VALIDATE_EMAIL);
      }

      /**
       * Match a value against another
       * 
       * @param string $value1
       * @param string $value2
       * @return bool
       */
      public static function match($value1, $value2) {
        $value1 = trim($value1);
        $value2 = trim($value2);
        return $value1 === $value2;
      }
}