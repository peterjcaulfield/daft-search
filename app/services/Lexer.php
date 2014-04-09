<?php

class Lexer
{
    protected static $tokenTypes = array(
            "/^[A-Z]{1}[a-z]+/" => "T_LOCATION",
            "/^\d{1}$/" => "T_SINGLE_DIGIT",
            "/^\d{2,}/" => "T_DIGIT",
            "/^\s/" => "T_WHITESPACE",
            "/^or/" => "T_LOGIC",
            "/^rent|let/" => "T_RENT",
            "/^sale|buy|purchase/" => "T_BUY",
            "/^in/" => "T_IDENTIFIER_SPECIFIER",
            "/^(\w+)/" => "T_IDENTIFIER"
    );


    protected static function tokenizeUntyped($str)
    {
       return explode(' ', $str);
    }

    public static function run($str)
    {
        $typedTokenMeta = array(
                "T_LOCATION" => array(
                    "occurrences" => 0,
                    "positions" => array()
                    ),
                "T_SINGLE_DIGIT" => array(
                    "occurrences" => 0,
                    "positions" => array()
                    ),
                "T_DIGIT" => array(
                    "occurrences" => 0,
                    "positions" => array()
                    ),
                "T_WHITESPACE" => array(
                    "occurrences" => 0,
                    "positions" => array()
                    ),
                "T_LOGIC" => array(
                    "occurrences" => 0,
                    "positions" => array()
                    ),
                "T_RENT" => array(
                        "occurrences" => 0,
                        "positions" => array()
                        ),
                "T_BUY" => array(
                        "occurrences" => 0,
                        "positions" => array()
                        ),
                "T_IDENTIFIER_SPECIFIER" => array(
                        "occurrences" => 0,
                        "positions" => array()
                        ),
                "T_IDENTIFIER" =>array(
                        "occurrences" => 0,
                        "positions" => array()
                        ),
                );

        $typedTokens = array();
        $typedTokenIndex = 0;
        $untypedTokens = static::tokenizeUntyped($str);

        foreach ( $untypedTokens as $untypedToken  )
        {
            $result = static::tokenizeTyped($untypedToken);

            if ( $result !== false )
            {
                array_push($typedTokens, $result);
                $typedTokenMeta[$result['token']]['occurrences']++;
                array_push($typedTokenMeta[$result['token']]['positions'], $typedTokenIndex);
                $typedTokenIndex++;
            }
        }

        return array('typedTokens' => $typedTokens, 'typedTokenMeta' => $typedTokenMeta);
    }

    protected static function tokenizeTyped($untypedToken)
    {
        foreach ( static::$tokenTypes as $pattern => $name )
        {
            if ( preg_match($pattern, $untypedToken, $matches) )
            {
                if ( $name == "T_WHITESPACE" )
                    return false;
                else
                    return array(
                        'match' => $matches[0],
                        'token' => $name
                    );
            }
        }
    }
}
