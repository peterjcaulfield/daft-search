<?php
/**
 * This class parses typed tokens in to formatted API request params
 */
class Parser
{
    protected $typedTokens;

    protected $parsed = array();

    protected $locations = array();

    protected $api;

    public function __construct()
    {
        $this->api = new SoapClient('http://api.daft.ie/v2/wsdl.xml');
        $this->parsed['params']['api_key'] = API_KEY;
    }

    /**
     * return the parsed params
     *
     * @return mixed
     */
    public function getParsed()
    {
        return $this->parsed;
    }

    /**
     * Attempt to convert a raw location identifier to an API area location id
     *
     * @return mixed
     */
    protected function getAreaIdFromRawLocation($rawLocation)
    {
        if ( !isset($this->locations['areas']) )
        {
            $response = $this->api->areas(array('api_key' => API_KEY, 'area_type' => 'area'));
            $this->locations['areas'] = $response->areas;
        }

        $areaId = false;

        foreach ( $this->locations['areas'] as $area )
        {
            if ( $area->name == $rawLocation )
            {
                $areaId = $area->id;
                break;
            }
        }

        return $areaId;
    }

    /**
     * Attempt to convert a raw location identifier to an API county location id
     *
     * @return mixed
     */
    protected function getCountyIdFromRawLocation($rawLocation)
    {
        if ( !isset($this->locations['counties']) )
        {
            $response = $this->api->areas(array('api_key' => API_KEY, 'area_type' => 'county'));
            $this->locations['counties'] = $response->areas;
        }

        $areaId = false;

        foreach ( $this->locations['counties'] as $county )
        {
            $countyFormatted = substr($county->name, 4);

            if ( $countyFormatted == $rawLocation )
            {
                $areaId = $county->id;
                break;
            }
        }

        return $areaId;
    }

    /**
     * Check if a specific token is present in the typed token array
     *
     * @return boolean
     */
    protected function tokenDidOccur($token)
    {
        if ( $this->typedTokens['typedTokenMeta'][$token]['occurrences'] )
            return true;
        else
            false;
    }

    /**
     * Check how many times a token occurred
     *
     * @return int
     */
    protected function getTokenOccurrences($token)
    {
        return $this->typedTokens['typedTokenMeta'][$token]['occurrences'];
    }

    /**
     * Get the matched token based on the token type
     *
     * @return mixed
     */
    protected function getMatchFromTokenType($token, $occurrence=0)
    {
        if ( isset($this->typedTokens['typedTokenMeta'][$token]['positions'][$occurrence]) )
            return $this->typedTokens['typedTokens'][$this->typedTokens['typedTokenMeta'][$token]['positions'][$occurrence]]['match'];
        else
            return false;
    }

    /**
     * Parse the API request verb
     *
     * @return string
     */
    protected function parseAdtype()
    {
        if ( $this->tokenDidOccur('T_RENT') )
            $this->parsed['ad_type'] = 'search_rental';
        else if ( $this->tokenDidOccur('T_BUY') )
            $this->parsed['ad_type'] = 'search_sale';
        else
            $this->parsed['ad_type'] = 'search_rental';
    }

    /**
     * Parse a price for the API request
     *
     * @return void
     */
    protected function parsePrice()
    {
        if ( $this->tokenDidOccur('T_DIGIT') )
            $this->parsed['params']['query']['max_price'] = $this->getMatchFromTokenType('T_DIGIT');
    }

    /**
     * Parse a location for the API request
     *
     * @return void
     */
    protected function parseLocation()
    {
        if ( $this->tokenDidOccur('T_LOCATION') )
        {
            $rawLocation = $this->getMatchFromTokenType('T_LOCATION');

            // now we have to generate an area or county id based on a raw value look up of API area data
            $areaId = $this->getAreaIdFromRawLocation($rawLocation);

            if ( $areaId )
            {
                $this->parsed['params']['query']['areas'] = array($areaId);
            }
            else
            {
                // no matching area was found so now we should search against county API data
                $countyId = $this->getCountyIdFromRawLocation($rawLocation);

                if ( $countyId )
                {
                    $this->parsed['params']['query']['counties'] = array($countyId);
                }
            }
        }
    }

    /**
     * Parse number of rooms for the API request
     *
     * @return void
     */
    protected function parseNumRooms()
    {
        switch($numRooms =  $this->getTokenOccurrences('T_SINGLE_DIGIT') )
        {
            case 1:
                $this->parsed['params']['query']['bedrooms'] = $this->getMatchFromTokenType('T_SINGLE_DIGIT');
                break;
            case ( $numRooms >= 2):
                $this->parsed['params']['query']['min_bedrooms'] = $this->getMatchFromTokenType('T_SINGLE_DIGIT');
                $this->parsed['params']['query']['max_bedrooms'] = $this->getMatchFromTokenType('T_SINGLE_DIGIT', 1);
                break;
        }
    }

    /**
     * Run the parser and return the parse result
     *
     * @return mixed
     */
    public function run($typedTokens)
    {
        $this->typedTokens = $typedTokens;

        $parseSteps = array('parseAdtype', 'parsePrice', 'parseNumRooms', 'parseLocation');

        foreach ( $parseSteps as $parseStep )
            $this->$parseStep();

        if ( !isset($this->parsed['params']['query']) )
            return false;

        return $this->parsed;
    }
}

