<?php

declare(strict_types=1);

namespace App\Component\FileReader;

use App\Component\FileReader\Exception\FileReaderException;
use Generator;
use Throwable;
use XMLReader;

class XmlFileReader
{

    private const SIMPLE_XML_ELEMENT = 'SimpleXMLElement';

    public function xmlReader($xmlPath): Generator
    {

        try {

            $xmlReader = new XMLReader();
            $xmlReader->open($xmlPath);

            $xmlReader->read();
            $xmlContent = $xmlReader->expand();
            // Identify parent node name which has text contents
            $parentNode = $xmlContent->firstChild->nextSibling->nodeName;
            // skip root node & traverse to reach parent node.
            while ($xmlReader->read() && $xmlReader->name != $parentNode);

            while($xmlReader->name == $parentNode) {
                if ($xmlReader->nodeType == XMLReader::ELEMENT) {
                    $xmlDataInArray  = ((array) simplexml_load_string($xmlReader->readOuterXML(),
                        self::SIMPLE_XML_ELEMENT, LIBXML_NOCDATA));

                    // Parsing SimpleXMLElement object to String
                    array_walk_recursive($xmlDataInArray, function(&$item){$item=strval($item);});

                    yield $xmlDataInArray;
                    $xmlReader->next($parentNode);
                }
            }
            $xmlReader->close();

        } catch (Throwable $exception){
            throw new FileReaderException("Error while reading XML file", [$exception]);
        }
    }

}