<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ComprehensiveReportExcelExporter
{
    public function write(array $data, ?string $from, ?string $to): void
    {
        $spreadsheet = $this->createWorkbook($data, $from, $to);
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
    }

    public function createWorkbook(array $data, ?string $from, ?string $to): Spreadsheet
    {
        $reservations = $data['reservations'];
        $maintenanceReports = $data['maintenanceReports'];
        $completedReservations = $reservations->where('status', 'completed');
        $confirmedReservations = $reservations->where('status', 'confirmed');
        $pendingReservations = $reservations->where('status', 'pending');
        $cancelledReservations = $reservations->where('status', 'cancelled');

        $financialMetrics = [
            $this->metric('Financial KPIs', 'Total Revenue', $data['totalRevenue'], 'currency'),
            $this->metric('Financial KPIs', 'Total Payments Received', $data['totalPaymentsReceived'], 'currency'),
            $this->metric('Financial KPIs', 'Revenue This Month', $data['revenueThisMonth'], 'currency'),
            $this->metric('Financial KPIs', 'Average Revenue Per Reservation', $data['averageRevenuePerReservation'], 'currency'),
        ];

        $reservationMetrics = [
            $this->metric('Reservation KPIs', 'Total Reservations', $reservations->count(), 'number'),
            $this->metric('Reservation KPIs', 'Confirmed Reservations', $confirmedReservations->count(), 'number', '2563EB'),
            $this->metric('Reservation KPIs', 'Pending Reservations', $pendingReservations->count(), 'number', 'EAB308'),
            $this->metric('Reservation KPIs', 'Cancelled Reservations', $cancelledReservations->count(), 'number', 'EF4444'),
        ];

        $occupancyMetrics = [
            $this->metric('Occupancy KPIs', 'Occupancy Rate', $data['occupancyRate'], 'percent', '16A34A'),
            $this->metric('Occupancy KPIs', 'Occupied Rooms', $data['occupiedRooms'], 'number', 'DC2626'),
            $this->metric('Occupancy KPIs', 'Available Rooms', $data['availableRooms'], 'number', '16A34A'),
            $this->metric('Occupancy KPIs', 'Rooms Under Maintenance', $data['maintenanceRooms'], 'number', 'EAB308'),
        ];

        $guestMetrics = [
            $this->metric('Guest KPIs', 'Total Guests', $data['totalGuests'], 'number'),
            $this->metric('Guest KPIs', 'New Guests', $data['newGuests'], 'number', '2563EB'),
            $this->metric('Guest KPIs', 'Returning Guests', $data['returningGuests'], 'number', '16A34A'),
            $this->metric('Guest KPIs', 'Average Length of Stay', $data['averageStayDuration'], 'days', '9333EA'),
        ];

        $maintenanceMetrics = [
            $this->metric('Maintenance KPIs', 'Total Reports', $maintenanceReports->count(), 'number'),
            $this->metric('Maintenance KPIs', 'Pending Issues', $data['maintenancePending'], 'number', 'EAB308'),
            $this->metric('Maintenance KPIs', 'Under Repair', $data['maintenanceRepairing'], 'number', 'F97316'),
            $this->metric('Maintenance KPIs', 'Completed', $data['maintenanceCompleted'], 'number', '16A34A'),
        ];

        $reports = [
            'Overall Summary' => [
                'title' => 'Comprehensive Report Summary',
                'cards' => [
                    $financialMetrics[0],
                    $reservationMetrics[0],
                    $occupancyMetrics[0],
                    $guestMetrics[0],
                ],
                'metrics' => [
                    ...array_map(fn ($metric) => [...$metric, 'section' => 'Financial'], $financialMetrics),
                    ...array_map(fn ($metric) => [...$metric, 'section' => 'Reservations'], $reservationMetrics),
                    ...array_map(fn ($metric) => [...$metric, 'section' => 'Occupancy'], $occupancyMetrics),
                    ...array_map(fn ($metric) => [...$metric, 'section' => 'Guests'], $guestMetrics),
                    ...array_map(fn ($metric) => [...$metric, 'section' => 'Maintenance'], $maintenanceMetrics),
                ],
                'charts' => [],
            ],
            'Financial' => [
                'title' => 'Financial Report',
                'metrics' => $financialMetrics,
                'charts' => [
                    $this->chart('Payment Method Breakdown', DataSeries::TYPE_DOUGHNUTCHART, $data['paymentMethodLabels'], $data['paymentMethodData'], 'Payments', true, DataSeries::DIRECTION_COL, 'number', ['0741FF', '03FF18', 'F65C97', 'F97316', 'EF4444']),
                    $this->chart('Revenue by Room Type', DataSeries::TYPE_BARCHART, $data['roomTypeRevenueLabels'], $data['roomTypeRevenueData'], 'Revenue (₱)', false, DataSeries::DIRECTION_COL, 'currency', ['8B5CF6', 'EC4899', 'F97316', '06B6D4', '14B8A6']),
                    $this->chart('Monthly Revenue', DataSeries::TYPE_LINECHART, $data['monthlyLabels'], $data['monthlyRevenue'], 'Monthly Revenue', true, DataSeries::DIRECTION_COL, 'currency', ['F97316']),
                    $this->chart('Revenue by Category', DataSeries::TYPE_BARCHART, $data['revenueByCategoryLabels'], $data['revenueByCategoryData'], 'Revenue (₱)', false, DataSeries::DIRECTION_BAR, 'currency', ['F97316', '3B82F6', '14B8A6', '8B5CF6']),
                ],
            ],
            'Reservations' => [
                'title' => 'Reservations Report',
                'metrics' => $reservationMetrics,
                'charts' => [
                    $this->chart('Reservation Trend', DataSeries::TYPE_LINECHART, $data['reservationTrendLabels'], $data['reservationTrendData'], 'Reservations', true, DataSeries::DIRECTION_COL, 'number', ['3B82F6']),
                    $this->chart('Reservation Status Distribution', DataSeries::TYPE_DOUGHNUTCHART, $data['reservationStatusLabels'], $data['reservationStatusData'], 'Reservations', true, DataSeries::DIRECTION_COL, 'number', ['F59E0B', '3B82F6', '10B981', 'EF4444']),
                    $this->chart('Most Booked Room Types', DataSeries::TYPE_BARCHART, $data['mostBookedRoomTypeLabels'], $data['mostBookedRoomTypeData'], 'Bookings', false, DataSeries::DIRECTION_COL, 'number', ['8B5CF6', 'EC4899', 'F97316', '06B6D4']),
                    $this->chart('Reservation by Number of Guests', DataSeries::TYPE_BARCHART, $data['reservationGuestLabels'], $data['reservationGuestData'], 'Reservations', false, DataSeries::DIRECTION_COL, 'number', ['F97316', '3B82F6', '14B8A6', '8B5CF6', 'EC4899']),
                ],
            ],
            'Occupancy' => [
                'title' => 'Occupancy Report',
                'metrics' => $occupancyMetrics,
                'charts' => [
                    $this->chart('Occupancy Trend', DataSeries::TYPE_LINECHART, $data['occupancyTrendLabels'], $data['occupancyTrendData'], 'Occupied Bookings', true, DataSeries::DIRECTION_COL, 'number', ['10B981']),
                    $this->chart('Room Status Distribution', DataSeries::TYPE_DOUGHNUTCHART, $data['roomStatusLabels'], $data['roomStatusData'], 'Rooms', true, DataSeries::DIRECTION_COL, 'number', ['10B981', 'EF4444', '3B82F6', 'F59E0B']),
                ],
            ],
            'Guests' => [
                'title' => 'Guests Report',
                'metrics' => $guestMetrics,
                'charts' => [
                    $this->chart('Guest Registration Trend', DataSeries::TYPE_LINECHART, $data['reservationTrendLabels'], $data['reservationTrendData'], 'Guest Registrations', true, DataSeries::DIRECTION_COL, 'number', ['8B5CF6']),
                    $this->chart('New vs Returning Guests', DataSeries::TYPE_DOUGHNUTCHART, ['New Guests', 'Returning Guests'], [$data['newGuests'], $data['returningGuests']], 'Guests', true, DataSeries::DIRECTION_COL, 'number', ['3B82F6', '10B981']),
                    $this->chart('Guests by Origin', DataSeries::TYPE_BARCHART, $data['guestOriginLabels'], $data['guestOriginData'], 'Guests', false, DataSeries::DIRECTION_BAR, 'number', ['8F0E16', 'D97706', '2563EB', '059669', '7C3AED']),
                    $this->chart('Average Stay Duration', DataSeries::TYPE_BARCHART, $data['stayDurationTrendLabels'], $data['stayDurationTrendData'], 'Average Stay Days', false, DataSeries::DIRECTION_COL, 'days', ['F59E0B', '10B981', '3B82F6', '8B5CF6', 'EF4444', '06B6D4']),
                ],
            ],
            'Maintenance' => [
                'title' => 'Maintenance Report',
                'description' => 'Review and manage reported room issues.',
                'metrics' => $maintenanceMetrics,
                'charts' => [
                    $this->chart('Report Status', DataSeries::TYPE_DOUGHNUTCHART, $data['maintenanceStatusLabels'], $data['maintenanceStatusData'], 'Reports', true, DataSeries::DIRECTION_COL, 'number', ['F59E0B', 'F97316', '3B82F6', '10B981']),
                    $this->chart('Priority Breakdown', DataSeries::TYPE_BARCHART, $data['maintenancePriorityLabels'], $data['maintenancePriorityData'], 'Reports', false, DataSeries::DIRECTION_COL, 'number', ['22C55E', 'EAB308', 'F97316', 'DC2626']),
                    $this->chart('Issues by Category', DataSeries::TYPE_BARCHART, $data['maintenanceCategoryLabels'], $data['maintenanceCategoryCounts'], 'Reports', false, DataSeries::DIRECTION_COL, 'number', ['3B82F6', '14B8A6', 'F97316', 'EAB308', '8B5CF6', 'EF4444']),
                ],
                'maintenanceReports' => $maintenanceReports,
            ],
        ];

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator('CASAUL Hotel')
            ->setTitle('Comprehensive Hotel Report')
            ->setSubject('Reports for '.($from ?: 'All').' to '.($to ?: 'All'));

        foreach ($reports as $sheetName => $report) {
            $sheet = $sheetName === 'Overall Summary'
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();
            $sheet->setTitle($sheetName);
            $this->writeReportSheet($sheet, $report, $from, $to);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function writeReportSheet(Worksheet $sheet, array $report, ?string $from, ?string $to): void
    {
        $sheet->getSheetView()->setZoomScale(85);
        $sheet->freezePane('A9');
        foreach (range('A', 'U') as $column) {
            $sheet->getColumnDimension($column)->setWidth(12.5);
        }
        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(16);

        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', $report['title']);
        $sheet->getStyle('A1:U1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->mergeCells('A2:U2');
        $sheet->setCellValue('A2', 'From: '.($from ?: 'All').'    To: '.($to ?: 'All'));
        $sheet->getStyle('A2')->getFont()->getColor()->setRGB('666666');

        if (! empty($report['description'])) {
            $sheet->mergeCells('A3:U3');
            $sheet->setCellValue('A3', $report['description']);
            $sheet->getStyle('A3')->getFont()->getItalic(true);
        }

        $cards = $report['cards'] ?? array_slice($report['metrics'], 0, 4);
        $this->writeKpiCards($sheet, $cards);

        $row = 9;
        if ($sheet->getTitle() === 'Overall Summary') {
            $row = $this->writeSummaryMetrics($sheet, $report['metrics'], $row);
        }

        $chartDataRow = max(48, 12 + (int) ceil(count($report['charts']) / 2) * 17);
        $this->writeChartsAndData($sheet, $report['charts'], $chartDataRow);

        if (! empty($report['maintenanceReports'])) {
            $row = $this->writeMaintenanceList($sheet, $report['maintenanceReports'], $chartDataRow);
        }

        $sheet->getStyle('A1:U'.max($row, $chartDataRow + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function writeKpiCards(Worksheet $sheet, array $metrics): void
    {
        $cardRanges = [
            ['C4:F4', 'C5:F6', 'C4:F6'],
            ['H4:K4', 'H5:K6', 'H4:K6'],
            ['M4:P4', 'M5:P6', 'M4:P6'],
            ['R4:U4', 'R5:U6', 'R4:U6'],
        ];

        foreach ($cardRanges as $index => [$labelRange, $valueRange, $cardRange]) {
            $metric = $metrics[$index] ?? null;
            if ($metric === null) {
                continue;
            }

            $sheet->mergeCells($labelRange);
            $sheet->mergeCells($valueRange);
            $labelCell = explode(':', $labelRange)[0];
            $valueCell = explode(':', $valueRange)[0];
            $sheet->setCellValue($labelCell, $metric['label']);
            $sheet->setCellValue($valueCell, $metric['value']);
            $sheet->getStyle($cardRange)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                'borders' => ['outline' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'D1D5DB']]],
            ]);
            $sheet->getStyle($labelCell)->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                'alignment' => ['vertical' => Alignment::VERTICAL_BOTTOM, 'wrapText' => true],
            ]);
            $sheet->getStyle($valueCell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 17, 'color' => ['rgb' => $metric['color']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
            ]);
            $sheet->getStyle($valueCell)->getNumberFormat()->setFormatCode($this->numberFormat($metric['format']));
        }

        $sheet->getRowDimension(4)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(24);
        $sheet->getRowDimension(6)->setRowHeight(15);
    }

    private function writeSummaryMetrics(Worksheet $sheet, array $metrics, int $row): int
    {
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", 'Report Statistics');
        $this->styleSectionHeader($sheet, "A{$row}:B{$row}");
        $row++;

        $currentSection = null;
        foreach ($metrics as $metric) {
            $section = $metric['section'];
            if ($section !== $currentSection) {
                $sheet->mergeCells("A{$row}:B{$row}");
                $sheet->setCellValue("A{$row}", $section);
                $this->styleSubsectionHeader($sheet, "A{$row}:B{$row}");
                $row++;
                $currentSection = $section;
            }

            $sheet->setCellValue("A{$row}", $metric['label']);
            $sheet->setCellValue("B{$row}", $metric['value']);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode($this->numberFormat($metric['format']));
            $row++;
        }

        return $row;
    }

    private function writeChartsAndData(Worksheet $sheet, array $charts, int $row): void
    {
        $positions = count($charts) === 3
            ? [['A10', 'G25'], ['H10', 'N25'], ['O10', 'U25']]
            : [['A10', 'J25'], ['K10', 'T25'], ['A27', 'J42'], ['K27', 'T42']];

        foreach ($charts as $index => $chartDefinition) {
            $labels = array_values($chartDefinition['labels']);
            $values = array_values($chartDefinition['values']);
            if ($labels === [] || $values === []) {
                $labels = ['No data'];
                $values = [0];
            }

            $sectionRow = $row;
            $sheet->mergeCells("A{$sectionRow}:C{$sectionRow}");
            $sheet->setCellValue("A{$sectionRow}", $chartDefinition['title']);
            $this->styleSectionHeader($sheet, "A{$sectionRow}:C{$sectionRow}");
            $headerRow = $sectionRow + 1;
            $sheet->setCellValue("A{$headerRow}", 'Category');
            $sheet->setCellValue("B{$headerRow}", $chartDefinition['series']);
            $this->styleColumnHeader($sheet, "A{$headerRow}:B{$headerRow}");

            $firstDataRow = $headerRow + 1;
            foreach ($labels as $pointIndex => $label) {
                $dataRow = $firstDataRow + $pointIndex;
                $sheet->setCellValue("A{$dataRow}", (string) $label);
                $sheet->setCellValue("B{$dataRow}", (float) ($values[$pointIndex] ?? 0));
                $sheet->getStyle("B{$dataRow}")->getNumberFormat()->setFormatCode($this->numberFormat($chartDefinition['format']));
            }
            $lastDataRow = $firstDataRow + count($labels) - 1;

            $sheetReference = "'".str_replace("'", "''", $sheet->getTitle())."'!";
            $categories = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $sheetReference.'$A$'.$firstDataRow.':$A$'.$lastDataRow,
                null,
                count($labels)
            );
            $seriesName = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                $sheetReference.'$B$'.$headerRow,
                null,
                1
            );
            $seriesValues = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                $sheetReference.'$B$'.$firstDataRow.':$B$'.$lastDataRow,
                $this->numberFormat($chartDefinition['format']),
                count($values)
            );
            if ($chartDefinition['colors'] !== []) {
                $seriesValues->setFillColor($chartDefinition['colors']);
            }
            $isBar = $chartDefinition['type'] === DataSeries::TYPE_BARCHART;
            $series = new DataSeries(
                $chartDefinition['type'],
                $isBar ? DataSeries::GROUPING_CLUSTERED : DataSeries::GROUPING_STANDARD,
                [0],
                [$seriesName],
                [$categories],
                [$seriesValues],
                $chartDefinition['direction'],
                false,
                $chartDefinition['type'] === DataSeries::TYPE_LINECHART ? DataSeries::STYLE_LINEMARKER : null
            );
            $legend = $chartDefinition['legend'] ? new Legend(Legend::POSITION_BOTTOM, null, false) : null;
            $chart = new Chart(
                'ReportChart'.($index + 1),
                new Title($chartDefinition['title']),
                $legend,
                new PlotArea(null, [$series])
            );
            [$topLeft, $bottomRight] = $positions[$index];
            $chart->setTopLeftPosition($topLeft)->setBottomRightPosition($bottomRight);
            $sheet->addChart($chart);

            $row = $lastDataRow + 3;
        }
    }

    private function writeMaintenanceList(Worksheet $sheet, iterable $reports, int $row): int
    {
        $row += 3;
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'Maintenance Reports');
        $this->styleSectionHeader($sheet, "A{$row}:F{$row}");
        $row++;

        $headers = ['Room', 'Issue', 'Priority', 'Reported By', 'Date & Time', 'Status'];
        foreach ($headers as $columnIndex => $header) {
            $column = chr(ord('A') + $columnIndex);
            $sheet->setCellValue("{$column}{$row}", $header);
        }
        $this->styleColumnHeader($sheet, "A{$row}:F{$row}");
        $headerRow = $row;
        $row++;

        foreach ($reports as $report) {
            $sheet->fromArray([
                $report->room_number,
                $report->problem ?: $report->category,
                $report->priority,
                $report->reported_by,
                optional($report->date_reported)->format('d/m/Y h:i A'),
                $report->status,
            ], null, "A{$row}");
            $row++;
        }

        if ($row === $headerRow + 1) {
            $sheet->setCellValue("A{$row}", 'No maintenance reports found for this date range.');
            $sheet->mergeCells("A{$row}:F{$row}");
            $row++;
        }

        $sheet->setAutoFilter("A{$headerRow}:F".($row - 1));
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(24);
        $sheet->getColumnDimension('F')->setWidth(18);

        return $row;
    }

    private function metric(string $section, string $label, int|float $value, string $format, string $color = '1F2937'): array
    {
        return compact('section', 'label', 'value', 'format', 'color');
    }

    private function chart(string $title, string $type, array $labels, array $values, string $series, bool $legend, string $direction = DataSeries::DIRECTION_COL, string $format = 'number', array $colors = []): array
    {
        return compact('title', 'type', 'labels', 'values', 'series', 'legend', 'direction', 'format', 'colors');
    }

    private function numberFormat(string $format): string
    {
        return match ($format) {
            'currency' => '"₱"#,##0.00',
            'percent' => '0.0"%"',
            'days' => '0.0" days"',
            default => '#,##0',
        };
    }

    private function styleSectionHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    private function styleSubsectionHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '800000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
        ]);
    }

    private function styleColumnHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            'borders' => ['bottom' => ['borderStyle' => 'thin', 'color' => ['rgb' => '9CA3AF']]],
        ]);
    }
}
