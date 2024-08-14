<?php


trait HelperTrait{
    public function guidv4($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }


    public function singularToPlural($word)
    {
        $rules = [
            '/(quiz)$/i' => "$1zes",
            '/^(ox)$/i' => "$1en",
            '/([m|l])ouse$/i' => "$1ice",
            '/(matr|vert|ind)(?:ix|ex)$/i' => "$1ices",
            '/(x|ch|ss|sh)$/i' => "$1es",
            '/([^aeiouy]|qu)y$/i' => "$1ies",
            '/(hive)$/i' => "$1s",
            '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
            '/sis$/i' => "ses",
            '/([ti])um$/i' => "$1a",
            '/(buffal|tomat)o$/i' => "$1oes",
            '/(bu)s$/i' => "$1ses",
            '/(alias|status)$/i' => "$1es",
            '/(octop|vir)us$/i' => "$1i",
            '/(ax|test)is$/i' => "$1es",
            '/s$/i' => "s",
            '/$/' => "s"
        ];

        foreach ($rules as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word);
            }
        }
        return $word;
    }
}