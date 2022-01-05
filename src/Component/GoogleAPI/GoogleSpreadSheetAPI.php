<?php

declare(strict_types=1);

namespace App\Component\GoogleAPI;

use App\Component\GoogleAPI\Exception\InvalidSpreadSheetIdException;
use App\Component\GoogleAPI\Exception\PermissionsErrorException;
use App\Interfaces\SpreadSheetInterface;
use Exception;
use Google\Service\Sheets\AppendValuesResponse;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use Psr\Log\LoggerInterface;
use Generator;

class GoogleSpreadSheetAPI implements SpreadSheetInterface
{
    private LoggerInterface $logger;
    private Google_Service_Sheets $googleSheet;
    private Google_Service_Drive $googleDrive;

    private const ERROR_CODE_MAX_LIMIT_REACHED = 429;
    private const WAIT_TIME_IN_SECONDS = 10;
    private const BATCH_LIMIT = 100;
    private const SHEET_NAME = 'Sheet1';


    public function __construct(
        LoggerInterface $logger,
        Google_Service_Sheets $googleSheet,
        Google_Service_Drive $googleDrive)
    {
        $this->logger = $logger;
        $this->googleDrive = $googleDrive;
        $this->googleSheet = $googleSheet;
    }

    private function create(): string
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

    private function write(string $sheetId, Generator $records)
    {
        try{
            if(empty($sheetId)){
                throw new InvalidSpreadSheetIdException("Spreadsheet id is empty.");
            } else {
                $this->updateRecordsAsBatch($sheetId, $records);
            }
        } catch (Exception $e) {
            $this->logger->error("Failed to write google sheet, sheet id : " . $sheetId,
                ["Error Message: ". $e->getMessage()]);
            exit;
        }
    }

    private function updateRecordsAsBatch(string $sheetId, Generator $records){
        $batchOfItems = [];
        $recordsCount = 0;
        foreach ($records as $record){
            $batchOfItems[] = $record;
            if(self::BATCH_LIMIT === count($batchOfItems)) {
                $result = $this->appendData($sheetId, $batchOfItems);
                $batchOfItems = [];
                if(isset($result))
                    $recordsCount += $result->getUpdates()->getUpdatedRows();
            }
        }

        if(!empty($batchOfItems)){
            $result = $this->appendData($sheetId, $batchOfItems);
            $recordsCount += $result->getUpdates()->getUpdatedRows();
        }
        echo $recordsCount . " rows has been updated in sheet => " . $sheetId;
    }

    private function appendData(string $sheetId, array $records): ?AppendValuesResponse
    {
        $result = null;
        try {
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $records
            ]);
            $params = [
                'valueInputOption' => "USER_ENTERED"
            ];

            $result =  $this->googleSheet->spreadsheets_values->append($sheetId, self::SHEET_NAME, $body, $params);
        } catch (Exception $ex){
            $this->logger->error("Error code: " . $ex->getCode() . " Error Message: " . $ex->getMessage());

            if($ex->getCode() === self::ERROR_CODE_MAX_LIMIT_REACHED){
                // when limit reached, wait for few seconds and resume writing.
                sleep(self::WAIT_TIME_IN_SECONDS);
                $result = $this->appendData($sheetId, $records);
            }
        }
        return $result;
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

    public function export(Generator $items): string {
        $sheetId = $this->create();
        $this->write($sheetId, $items);
        return $sheetId;
    }

}