<?php
/**
 * The application
 *
 * This file contains the core app logic and is responsible for returning
 * a response to incoming GET requests
 */
class App
{
    protected $parser;

    protected $api;

    public function __construct(Parser $parser, SoapClient $api)
    {
        $this->parser = $parser;
        $this->api = $api;
    }

    /**
     * Run the application and return a json response
     *
     * @return JSON
     */
    public function run()
    {
        $rawSearch = $_GET['search'];

        $data = $this->getAds($rawSearch);

        if ( $data )
        {
            if ( $data->results->pagination->total_results )
                echo $this->jsonResponse(200, $data);
            else
                echo $this->jsonResponse(204, $data);

        }
        else
            // couldnt parse request
            echo $this->jsonResponse(400);
    }

    /**
     * If we have a succesfully parsed request execute an API request
     *
     * @return mixed
     */
    protected function getAds($rawQuery)
    {
        if ( $requestParams = $this->getRequestParams($rawQuery) )
            return $this->queryAPI($requestParams);
        else
            return false;
    }

    /**
     * Attempt to parse the raw GET request
     *
     * @return mixed
     */
    protected function getRequestParams($rawQuery)
    {
        $typedTokens = Lexer::run($rawQuery);

        if ( $typedTokens )
        {
            $parsed = $this->parser->run($typedTokens);
            if ( $parsed )
                return $parsed;
            else
                return false;
        }
        return false;
    }

    /**
     * execute an API request with the parsed params
     *
     * @return object
     */
    protected function queryAPI($request)
    {
        $requestVerb = $request['ad_type'];

        $requestParams = $request['params'];

        return  $this->api->$requestVerb($requestParams);
    }

    /**
     * Format a JSON response
     *
     * @return JSON
     */
    protected function jsonResponse($code, $data=array())
    {
        return json_encode(array(
                    'code' => $code,
                    'data' => $data
                    )
                );
    }

}
