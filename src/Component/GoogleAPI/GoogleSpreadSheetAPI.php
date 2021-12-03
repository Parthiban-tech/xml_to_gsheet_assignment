<?php

declare(strict_types=1);

namespace App\Component\GoogleAPI;

use App\Component\GoogleAPI\Exception\InvalidSpreadSheetIdException;
use App\Component\GoogleAPI\Exception\PermissionsErrorException;
use App\Interfaces\SpreadSheetInterface;
use Exception;
//use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use Psr\Log\LoggerInterface;

class GoogleSpreadSheetAPI implements SpreadSheetInterface
{
    private LoggerInterface $logger;
    //private Google_Client $googleClient;
    private Google_Service_Sheets $googleSheet;
    private Google_Service_Drive $googleDrive;
    private string $sheetName = 'Sheet1';

    public function __construct(
        LoggerInterface $logger,
        //Google_Client $googleClient,
        Google_Service_Sheets $googleSheet,
        Google_Service_Drive $googleDrive)
        //string $sheetName)
    {
        // $googleClient->setScopes([Google_Service_Sheets::SPREADSHEETS, Google_Service_Drive::DRIVE]);
        $this->logger = $logger;
        //$this->googleClient = $googleClient;
        $this->googleDrive = $googleDrive;
        $this->googleSheet = $googleSheet;
        //$this->sheetName = $sheetName;
    }

    public function create(): string
    {
        try {
            $requestBody = new Google_Service_Sheets_Spreadsheet();
            $response = $this->googleSheet->spreadsheets->create($requestBody);
            $sheetId = $response->spreadsheetId;
            $this->setSheetPermissions($sheetId);
        } catch (Exception $e) {
            $this->logger->error("Failed to create google sheet : " . $e->getMessage());
            exit;
        }

        $this->logger->info("Google Sheet has been created, Id: " . $sheetId);
        return $sheetId;
    }

    public function write(string $sheetId, array $data, string $range)
    {
        try{
            if(empty($sheetId)){
                throw new InvalidSpreadSheetIdException("Spreadsheet id is empty.");
            } else {
                $body = new Google_Service_Sheets_ValueRange([
                    'values'=>$data
                ]);
                $params = [
                    'valueInputOption' => 'RAW'
                ];
                $this->googleSheet->spreadsheets_values->update($sheetId, $range, $body, $params);
            }
        } catch (Exception $e) {
            $this->logger->error("Failed to write google sheet, sheet id : " . $sheetId,
                ["Error Message: ". $e->getMessage()]);
            exit;
        }
    }

    public function read(string $sheetId, string $range)
    {
        try{
            $response = $this->googleSheet->spreadsheets_values->get($sheetId, $range);
            $values = $response->getValues();

        } catch (Exception $e) {
            $this->logger->error("Failed to read google sheet, sheet id : " . $sheetId,
                ["Error Message: ". $e->getMessage()]);
            exit;
        }
        return $values;
    }

    private function setSheetPermissions(string $sheetId): void
    {
        $googleServiceDrive = $this->googleDrive;

        $permission = new Google_Service_Drive_Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        try {
            $googleServiceDrive->permissions->create($sheetId, $permission);
        } catch (Exception $e) {
            throw new PermissionsErrorException('Error setting sheet permissions. ' . $e->getMessage());
        }
    }

    public function exportToSpreadsheet(array $spreadSheetData): string {
        $sheetId = $this->create();
        $this->write($sheetId, $spreadSheetData, $this->sheetName);
        return $sheetId;
    }
}