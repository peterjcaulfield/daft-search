<?php

class App
{
    protected $parser;

    protected $api;

    public function __construct(Parser $parser, SoapClient $api)
    {
        $this->parser = $parser;
        $this->api = $api;
    }

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

    protected function getAds($rawQuery)
    {
        if ( $requestParams = $this->getRequestParams($rawQuery) )
            return $this->queryAPI($requestParams);
        else
            return false;
    }

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

    protected function queryAPI($request)
    {
        $requestVerb = $request['ad_type'];

        $requestParams = $request['params'];

        return  $this->api->$requestVerb($requestParams);
    }

    protected function jsonResponse($code, $data=array())
    {
        return json_encode(array(
                    'code' => $code,
                    'data' => $data
                    )
                );
    }

}
