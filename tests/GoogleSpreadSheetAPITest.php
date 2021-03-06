<?php

declare(strict_types=1);

namespace App\Tests;

use App\Component\GoogleAPI\GoogleSpreadSheetAPI;
use Google\Service\Sheets\UpdateValuesResponse;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Drive_Resource_Permissions;
use Google_Service_Sheets;
use Google_Service_Sheets_Resource_Spreadsheets;
use Google_Service_Sheets_Resource_SpreadsheetsValues;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use App\Tests\data\DataProvider;

class GoogleSpreadSheetAPITest extends TestCase
{
    /**
     * @var Google_Service_Drive|Google_Service_Sheets|mixed|MockObject
     */
    private mixed $googleSheetServiceMock;
    /**
     * @var Google_Service_Drive|mixed|MockObject
     */
    private mixed $googleSheetDriveMock;

    /**
     * @var mixed|MockObject|LoggerInterface
     */
    private mixed $loggerMock;
    /**
     * @var Google_Service_Sheets_Resource_Spreadsheets|mixed|MockObject
     */
    private mixed $sheetResourceMock;

    /**
     * @var Google_Service_Drive_Resource_Permissions|mixed|MockObject
     */
    private mixed $drivePermissionResourceMock;
    /**
     * @var Google_Service_Sheets_Resource_SpreadsheetsValues|mixed|MockObject
     */
    private mixed $spreadsheetsValuesResourceMock;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->googleSheetServiceMock = $this->createMock(Google_Service_Sheets::class);
        $this->googleSheetDriveMock = $this->createMock(Google_Service_Drive::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->drivePermissionResourceMock = $this->createMock(Google_Service_Drive_Resource_Permissions::class);
        $this->googleSheetDriveMock->permissions = $this->drivePermissionResourceMock;

        // Mock for calling create function which requires object of @class Google_Service_Sheets_Resource_Spreadsheets
        $this->sheetResourceMock = $this->createMock(Google_Service_Sheets_Resource_Spreadsheets::class);
        $this->googleSheetServiceMock->spreadsheets = $this->sheetResourceMock;

        // Mock for calling update function which requires object of @class Google_Service_Sheets_Resource_SpreadsheetsValues
        $this->spreadsheetsValuesResourceMock = $this->createMock(Google_Service_Sheets_Resource_SpreadsheetsValues::class);
        $this->googleSheetServiceMock->spreadsheets_values = $this->spreadsheetsValuesResourceMock;

    }

    /** @test */
    public function it_exports_to_spreadsheet_process_with_dummy_data(){

        $resultSheetObj = new Google_Service_Sheets_Spreadsheet();
        $spreadsheetId = uniqid();
        $resultSheetObj->spreadsheetId = $spreadsheetId;

        $this->sheetResourceMock->expects(static::once())
            ->method('create')
            ->with(new Google_Service_Sheets_Spreadsheet())
            ->willReturn($resultSheetObj);

        $permission = new Google_Service_Drive_Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        $this->drivePermissionResourceMock->expects(static::once())
            ->method('create')
            ->with($resultSheetObj->spreadsheetId, $permission)
            ->willReturn(true);

        $dataProvider = new DataProvider();
        $body = new Google_Service_Sheets_ValueRange([ 'values'=> $dataProvider->inputDataToExport() ]);
        $params = [ 'valueInputOption' => 'RAW' ];


        $this->spreadsheetsValuesResourceMock->expects(static::once())
            ->method('update')
            ->with($resultSheetObj->spreadsheetId, 'Sheet1', $body, $params)
            //->willReturn(true);
            ->willReturn(new UpdateValuesResponse());

        $googleSpreadSheetAPI = new GoogleSpreadSheetAPI(
            $this->loggerMock,
            $this->googleSheetServiceMock,
            $this->googleSheetDriveMock
        );
        $resultSheetId = $googleSpreadSheetAPI->exportToSpreadsheet($dataProvider->inputDataToExport());
        $this->assertEquals($spreadsheetId, $resultSheetId);
    }
}