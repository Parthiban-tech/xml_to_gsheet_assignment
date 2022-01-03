<?php

declare(strict_types=1);

namespace App\Utility\FileReader;

use App\Utility\FileReader\Exception\FileNotExistException;
use App\Interfaces\FileReaderInterface;
use XMLReader;
use Generator;

class FileReaderLocal implements FileReaderInterface
{
    private const SIMPLE_XML_ELEMENT = 'SimpleXMLElement';
    private string $resourceDir;

    public function __construct( string $resourceDir )
    {
        $this->resourceDir = $resourceDir;
    }

    public function read(string $xmlFileName): Generator
    {
        $xmlAbsPath  = $this->resourceDir . $xmlFileName;

        if(false == file_exists($xmlAbsPath)) {
            throw new FileNotExistException("File does not exist in path: " . $xmlAbsPath);
        }

        $xmlReader = new XMLReader();
        $xmlReader->open($xmlAbsPath);

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
    }
}