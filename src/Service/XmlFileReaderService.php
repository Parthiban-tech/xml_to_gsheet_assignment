<?php

declare(strict_types=1);

namespace App\Service;


use App\Application\Constant\AppConstants;
use App\Interfaces\FileReaderInterface;
use App\Interfaces\IFTPAdapter;
use App\Service\Exception\FileNotExistException;
use App\Service\Exception\FTPGetFileToLocalException;
use App\Service\Exception\FTPLoginFailedException;
use App\Service\Exception\FTPServerConnectionRefusedException;
use App\Service\Exception\InvalidSourceTypeException;
use XMLReader;

class XmlFileReaderService implements FileReaderInterface
{
    protected IFTPAdapter $iFTPAdapter;
    private string $resourceDir;
    private string $ftpServer;
    private string $ftpUser;
    private string $ftpPassword;
    const SIMPLE_XML_ELEMENT = 'SimpleXMLElement';

    public function __construct(IFTPAdapter $iFTPAdapter, string $resourceDir, string $ftpHost, string $ftpUser, string $ftpPassword)
    {
        $this->iFTPAdapter = $iFTPAdapter;
        $this->resourceDir = $resourceDir;
        $this->ftpServer = $ftpHost;
        $this->ftpUser = $ftpUser;
        $this->ftpPassword = $ftpPassword;
    }

    public function read(string $sourceType, string $xmlFileName): array
    {
        return match ($sourceType) {
            AppConstants::OPTION_SOURCE_LOCAL => $this->readXmlFromResourceDir($xmlFileName),
            AppConstants::OPTION_SOURCE_FTP => $this->readXmlFromFTP($xmlFileName),
            default => throw new InvalidSourceTypeException("Invalid source type: " . $sourceType)
        };
    }

    private function readXmlFromResourceDir(string $xmlFileName): array
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

        $dataAsArray = array();
        while($xmlReader->name == $nodeName) {
            if ($xmlReader->nodeType == XMLReader::ELEMENT) {

                $arrStr  = ((array) simplexml_load_string($xmlReader->readOuterXML(),
                    self::SIMPLE_XML_ELEMENT, LIBXML_NOCDATA));
                // Convert SimpleXMLElement object to String
                array_walk_recursive($arrStr,function(&$item){$item=strval($item);});
                $dataAsArray[] = $arrStr;
                $xmlReader->next($nodeName);
            }
        }
        $xmlReader->close();
        return $dataAsArray;
    }

    private function readXmlFromFTP($xmlFileName): array
    {
        $connection = $this->iFTPAdapter->connect($this->ftpServer);
        if(false === $connection){
            throw new FTPServerConnectionRefusedException("Unable to connect to FTP server: " . $connection);
        }

        if(false === $this->iFTPAdapter->login($connection, $this->ftpUser, $this->ftpPassword)){
            throw new FTPLoginFailedException("Unable to login server with user " . "'" . $this->ftpUser . "'");
        }

        $this->iFTPAdapter->enablePassiveMode($connection);
        if(false === $this->iFTPAdapter->getFile($connection, $this->resourceDir, $xmlFileName)){
            throw new FTPGetFileToLocalException("Failed to get file or store file in local. Local path: " . $this->resourceDir);
        }

        $this->iFTPAdapter->closeConnection($connection);

        return $this->readXmlFromResourceDir($xmlFileName);
    }
}