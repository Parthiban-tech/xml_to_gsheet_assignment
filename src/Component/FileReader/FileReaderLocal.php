<?php

declare(strict_types=1);

namespace App\Component\FileReader;

use App\Component\FileReader\Exception\FileNotExistException;
use App\Interfaces\FileReaderInterface;
use XMLReader;

class FileReaderLocal implements FileReaderInterface
{
    private const SIMPLE_XML_ELEMENT = 'SimpleXMLElement';
    private string $resourceDir;

    public function __construct( string $resourceDir )
    {
        $this->resourceDir = $resourceDir;
    }

    public function read(string $xmlFileName): array
    {
        $xmlAbsPath  = $this->resourceDir . $xmlFileName;

        if(false == file_exists($xmlAbsPath)) {
            throw new FileNotExistException("File does not exist in path: " . $xmlAbsPath);
        }

        $xmlReader = new XMLReader();
        $xmlReader->open($xmlAbsPath);

        $xmlReader->read();
        $node = $xmlReader->expand();

        // Identify parent node name which has text contents
        $nodeName = $node->firstChild->nextSibling->nodeName;

        // skip root node
        while ($xmlReader->read() && $xmlReader->name != $nodeName);

        $xmlDataAsArray = array();
        while($xmlReader->name == $nodeName) {
            if ($xmlReader->nodeType == XMLReader::ELEMENT) {
                $arrStr  = ((array) simplexml_load_string($xmlReader->readOuterXML(),
                    self::SIMPLE_XML_ELEMENT, LIBXML_NOCDATA));

                // Parsing SimpleXMLElement object to String
                array_walk_recursive($arrStr, function(&$item){$item=strval($item);});
                $xmlDataAsArray[] = $arrStr;
                $xmlReader->next($nodeName);
            }
        }
        $xmlReader->close();
        return $xmlDataAsArray;
    }
}