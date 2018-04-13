<?php

if (! function_exists('plural')) {
    function plural($s)
    {
        return $s . 's';
    }
}

if (! function_exists('bool2str')) {
    function bool2str($b)
    {
        return $b ? 'true' : 'false';
    }
}

if (! function_exists('str2bool')) {
    function str2bool($s)
    {
        return $s == 'true';
    }
}

if (! function_exists('srvArg')) {
    function srvArg($name)
    {
        return new \Mr\Bootstrap\ContainerServiceArg($name);
    }
}

if (! function_exists('xml2arr')) {
    /**
     * @param string $stream XML
     * @return array
     */
    function xml2arr($stream)
    {
        return json_decode(json_encode(new \SimpleXMLElement($stream)), true);
    }
}

if (! function_exists('arr2xml')) {
    /**
     * Root node is ignored
     *
     * @param array $data
     * @return SimpleXMLElement
     */
    function arr2xml(array $data)
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

if (! function_exists('prettifyXml')) {
    function prettifyXml($xml)
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml instanceof \SimpleXMLElement ? $xml->asXML() : $xml);

        return $dom->saveXML();
    }
}

if (! function_exists('dump')) {
    function dump($value)
    {
        if (is_array($value)) {
            print_r($value);
        } else {
            var_dump($value);
        }
    }
}

if (! function_exists('dd')) {
    function dd($value)
    {
        dump($value);

        die(' -------- dump -------- ' . PHP_EOL);
    }
}

if (! function_exists('da')) {
    function da(\AwsElemental\Sdk\Interfaces\DataEntityInterface $obj)
    {
        dd($obj->toArray());
    }
}