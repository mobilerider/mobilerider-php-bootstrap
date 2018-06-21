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
        $xml = mr_arr2xml($data);

        return $pretty ? mr_prettify_xml($xml) : $xml->asXML();
    }

    /**
     * @inheritdoc
     */
    public function decode($stream)
    {
        return mr_xml2arr($stream);
    }
}