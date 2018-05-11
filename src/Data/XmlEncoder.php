<?php

namespace Mr\Bootstrap\Data;

use Mr\Bootstrap\Interfaces\ArrayEncoder;

class XmlEncoder implements ArrayEncoder
{
    /**
     * @inheritdoc
     */
    public function encode(array $data, $pretty = false)
    {
        if (!$data) {
            return '';
        }

        // Important root node is ignored
        $xml = arr2xml($data);

        return $pretty ? prettifyXml($xml) : $xml->asXML();
    }

    /**
     * @inheritdoc
     */
    public function decode($stream)
    {
        return xml2arr($stream);
    }
}