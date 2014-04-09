<?php

return new App(
        new Parser(),
        new SoapClient(
            "http://api.daft.ie/v2/wsdl.xml",
            array('features' => SOAP_SINGLE_ELEMENT_ARRAYS)
            )
        );


