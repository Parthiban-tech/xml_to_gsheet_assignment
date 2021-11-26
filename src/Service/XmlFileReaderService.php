<?php

declare(strict_types=1);

namespace App\Service;

use App\Application\Constant\AppConstants;
use App\Interfaces\FileReaderInterface;
use App\Interfaces\FTPAdapterInterface;
use App\Service\Exception\FileNotExistException;
use App\Service\Exception\FTPGetFileToLocalException;
use App\Service\Exception\FTPLoginFailedException;
use App\Service\Exception\FTPServerConnectionRefusedException;
use App\Service\Exception\InvalidParameterException;
use XMLReader;

class XmlFileReaderService implements FileReaderInterface
{
    private const SIMPLE_XML_ELEMENT = 'SimpleXMLElement';
    private const FTP_SERVER = 'server';
    private const FTP_USER = 'user';
    private const FTP_PASSWORD = 'password';

    protected FTPAdapterInterface $iFTPAdapter;
    private string $resourceDir;
    private string $ftpServer;
    private string $ftpUser;
    private string $ftpPassword;

    public function __construct(
        FTPAdapterInterface $iFTPAdapter,
        string              $resourceDir,
        array               $ftp
    )
    {
        $this->iFTPAdapter = $iFTPAdapter;
        $this->resourceDir = $resourceDir;
        $this->ftpServer = $ftp[self::FTP_SERVER];
        $this->ftpUser = $ftp[self::FTP_USER];
        $this->ftpPassword = $ftp[self::FTP_PASSWORD];
    }

    public function read(string $sourceType, string $xmlFileName): array
    {
        return match ($sourceType) {
            AppConstants::OPTION_SOURCE_LOCAL => $this->readXmlFromResourceDir($xmlFileName),
            AppConstants::OPTION_SOURCE_FTP => $this->readXmlFromFTP($xmlFileName),
            default => throw new InvalidParameterException("Invalid source type: " . $sourceType)
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