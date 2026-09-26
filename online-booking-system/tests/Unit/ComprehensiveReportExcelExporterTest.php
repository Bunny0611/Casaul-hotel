<?php

namespace Tests\Unit;

use App\Support\ComprehensiveReportExcelExporter;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ComprehensiveReportExcelExporterTest extends TestCase
{
    public function test_it_writes_all_report_sheets_and_native_charts_to_xlsx(): void
    {
        $exporter = new ComprehensiveReportExcelExporter;
        $workbook = $exporter->createWorkbook($this->reportData(), '2026-01-01', '2026-06-30');
        $expectedSheets = ['Overall Summary', 'Financial', 'Reservations', 'Occupancy', 'Guests', 'Maintenance'];

        $this->assertSame($expectedSheets, $workbook->getSheetNames());
        $this->assertSame(17, array_sum(array_map(
            fn ($sheet) => count($sheet->getChartCollection()),
            $workbook->getAllSheets()
        )));

        $filePath = tempnam(sys_get_temp_dir(), 'comprehensive-report-');
        try {
            $writer = new XlsxWriter($workbook);
            $writer->setIncludeCharts(true);
            $writer->save($filePath);

            $reader = new XlsxReader;
            $reader->setIncludeCharts(true);
            $savedWorkbook = $reader->load($filePath);

            $this->assertSame($expectedSheets, $savedWorkbook->getSheetNames());
            $this->assertSame(17, array_sum(array_map(
                fn ($sheet) => count($sheet->getChartCollection()),
                $savedWorkbook->getAllSheets()
            )));
            $this->assertSame(
                'From: 2026-01-01    To: 2026-06-30',
                $savedWorkbook->getSheetByName('Financial')->getCell('A2')->getValue()
            );
            $financialSheet = $savedWorkbook->getSheetByName('Financial');
            $this->assertSame('Total Revenue', $financialSheet->getCell('C4')->getValue());
            $this->assertSame(1200.5, $financialSheet->getCell('C5')->getValue());
            $reservationSheet = $savedWorkbook->getSheetByName('Reservations');
            $this->assertSame(
                ['Total Reservations', 'Confirmed Reservations', 'Pending Reservations', 'Cancelled Reservations'],
                array_map(fn ($cell) => $reservationSheet->getCell($cell)->getValue(), ['C4', 'H4', 'M4', 'R4'])
            );
            $this->assertSame('2563EB', $reservationSheet->getStyle('H5')->getFont()->getColor()->getRGB());
            $this->assertSame('#,##0', $reservationSheet->getStyle('C5')->getNumberFormat()->getFormatCode());
            $paymentChart = $financialSheet->getChartCollection()[0];
            $paymentSeries = $paymentChart->getPlotArea()->getPlotGroup()[0];
            $this->assertSame(
                ['0741FF', '03FF18'],
                $paymentSeries->getPlotValues()[0]->getFillColor()
            );
        } finally {
            @unlink($filePath);
            $workbook->disconnectWorksheets();
        }
    }

    private function reportData(): array
    {
        return [
            'reservations' => new Collection([
                (object) ['status' => 'completed'],
                (object) ['status' => 'confirmed'],
                (object) ['status' => 'pending'],
                (object) ['status' => 'cancelled'],
            ]),
            'maintenanceReports' => new Collection,
            'totalRevenue' => 1200.50,
            'totalPaymentsReceived' => 1200.50,
            'revenueThisMonth' => 250.00,
            'averageRevenuePerReservation' => 1200.50,
            'occupancyRate' => 50,
            'occupiedRooms' => 5,
            'availableRooms' => 4,
            'maintenanceRooms' => 1,
            'totalGuests' => 3,
            'newGuests' => 2,
            'returningGuests' => 1,
            'averageStayDuration' => 2.5,
            'maintenancePending' => 0,
            'maintenanceRepairing' => 0,
            'maintenanceCompleted' => 0,
            'paymentMethodLabels' => ['Cash', 'Card'],
            'paymentMethodData' => [2, 2],
            'roomTypeRevenueLabels' => ['Standard', 'Deluxe'],
            'roomTypeRevenueData' => [500, 700.50],
            'monthlyLabels' => ['Jan 2026', 'Feb 2026'],
            'monthlyRevenue' => [500, 700.50],
            'revenueByCategoryLabels' => ['Room', 'Facilities', 'Dining', 'Events'],
            'revenueByCategoryData' => [500, 200, 300, 200.50],
            'reservationTrendLabels' => ['Jan 2026', 'Feb 2026'],
            'reservationTrendData' => [2, 2],
            'reservationStatusLabels' => ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
            'reservationStatusData' => [1, 1, 1, 1],
            'mostBookedRoomTypeLabels' => ['Standard'],
            'mostBookedRoomTypeData' => [2],
            'reservationGuestLabels' => ['1 Guest', '2 Guests', '3 Guests', '4 Guests', '5+ Guests'],
            'reservationGuestData' => [1, 1, 1, 1, 0],
            'occupancyTrendLabels' => ['Jan 2026', 'Feb 2026'],
            'occupancyTrendData' => [3, 5],
            'roomStatusLabels' => ['Available', 'Occupied', 'Reserved', 'Maintenance'],
            'roomStatusData' => [4, 5, 0, 1],
            'guestOriginLabels' => ['Local', 'Provincial', 'Regional', 'National', 'International'],
            'guestOriginData' => [1, 1, 0, 1, 0],
            'stayDurationTrendLabels' => ['Jan 2026', 'Feb 2026'],
            'stayDurationTrendData' => [2, 3],
            'maintenanceStatusLabels' => ['Pending', 'Repairing', 'In Progress', 'Completed'],
            'maintenanceStatusData' => [0, 0, 0, 0],
            'maintenancePriorityLabels' => ['Low', 'Medium', 'High', 'Urgent'],
            'maintenancePriorityData' => [0, 0, 0, 0],
            'maintenanceCategoryLabels' => [],
            'maintenanceCategoryCounts' => [],
        ];
    }
}
