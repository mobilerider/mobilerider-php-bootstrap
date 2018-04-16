<?php

namespace Mr\Bootstrap\Data;



use Mr\Bootstrap\Interfaces\DataTransformerInterface;

class XmlTransformerInterface implements DataTransformerInterface
{
    public function transform(array $data, $pretty = false)
    {
        if (!$data) {
            return '';
        }

        // Important root node is ignored
        $xml = arr2xml($data);

        return $pretty ? prettifyXml($xml) : $xml->asXML();
    }

    public function toArray($stream)
    {
        return xml2arr($stream);
    }
}