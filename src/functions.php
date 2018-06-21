<?php

if (! function_exists('mr_logger')) {
    /**
     * Return unique instance of Logger
     * @param null $message
     * @param string $level
     * @param array $context
     * @return \Mr\Bootstrap\Utils\Logger
     */
    function mr_logger($message = null, $level = \Psr\Log\LogLevel::INFO, array $context = array())
    {
        /**
         * @var \Mr\Bootstrap\Utils\Logger
         */
        $logger = \Mr\Bootstrap\Utils\Logger::getInstance();

        if (! is_null($message)) {
            $logger->log($level, $message, $context);
        }

        return $logger;
    }
}

if (! function_exists('mr_plural')) {
    function mr_plural($s)
    {
        if (! $s) {
            return '';
        }

        if (lrv_ends_with($s, 'y')) {
            return substr($s, 0, -1) . 'ies';
        }

        return $s . 's';
    }
}

if (! function_exists('mr_bool2str')) {
    function mr_bool2str($b)
    {
        return $b ? 'true' : 'false';
    }
}

if (! function_exists('mr_str2bool')) {
    function mr_str2bool($s)
    {
        return $s == 'true';
    }
}

if (! function_exists('mr_srv_arg')) {
    function mr_srv_arg($name)
    {
        return new \Mr\Bootstrap\ContainerServiceArg($name);
    }
}

if (! function_exists('mr_xml2arr')) {
    /**
     * @param string $stream XML
     * @return array
     */
    function mr_xml2arr($stream)
    {
        return json_decode(json_encode(new \SimpleXMLElement($stream)), true);
    }
}

if (! function_exists('mr_arr2xml')) {
    /**
     * Root node is ignored
     *
     * @param array $data
     * @return SimpleXMLElement
     */
    function mr_arr2xml(array $data)
    {
        // Data needs to have a unique root string key
        $root = key($data);

        if (!is_string($root)) {
            // TODO: improve this
            throw new \RuntimeException('Data must have root string key');
        }

        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><$root/>");

        $addChild = function (\SimpleXMLElement $xml, $addChild, $value, $key = null) {
            if ($key == '@attributes') {
                foreach ($value as $k => $v) {
                    $xml->addAttribute($k, $v);
                }

                return $xml;
            }

            if (is_array($value)) {
                $isNumericArray = isset($value[0]);
                $xml = !is_null($key) && !$isNumericArray ? $xml->addChild($key) : $xml;

                foreach ($value as $k => $v) {
                    $addChild($xml, $addChild, $v, $isNumericArray ? $key : $k);
                }
            } else {
                switch (gettype($value)) {
                    case 'boolean':
                        $value = $value ? 'true' : 'false';
                        break;
                    default:
                        $value = (string)$value;
                }
                $xml->addChild($key, $value);
            }
        };

        $addChild($xml, $addChild, $data[$root]);

        return $xml;
    }
}

if (! function_exists('mr_prettify_xml')) {
    function mr_prettify_xml($xml)
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml instanceof \SimpleXMLElement ? $xml->asXML() : $xml);

        return $dom->saveXML();
    }
}

if (! function_exists('mr_dump')) {
    function mr_dump($value)
    {
        if (is_array($value)) {
            print_r($value);
        } else {
            var_dump($value);
        }
    }
}

if (! function_exists('mr_dd')) {
    function mr_dd($value)
    {
        mr_dump($value);

        die(' -------- dump -------- ' . PHP_EOL);
    }
}

if (! function_exists('da')) {
    function mr_da(\Mr\Bootstrap\Interfaces\DataEntityInterface $obj)
    {
        mr_dd($obj->toArray());
    }
}