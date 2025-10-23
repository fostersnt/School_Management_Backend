<?php

namespace App\Jobs;

use App\Models\ExcelFileReading;
use App\Models\UserData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReadingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $files = ExcelFileReading::all();
        if (count($files) > 0) {
            foreach ($files as $file) {
                $spreadsheet = IOFactory::load($stotage_path($file));

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $worksheet->toArray();

            foreach ($rows as $row) {
                // Assuming the first column contains the primary key or unique identifier for each record
                $primaryKey = $row[0];

                // Check if the record already exists in the database
                $record = UserData::where('your_unique_column', $primaryKey)->first();

                if ($record) {
                    // Update the record if it already exists
                    $record->update([
                        'column1' => $row[1],
                        'column2' => $row[2],
                        // Add more columns as needed
                    ]);
                } else {
                    // Create a new record if it doesn't exist
                    UserData::create([
                        'your_unique_column' => $primaryKey,
                        'column1' => $row[1],
                        'column2' => $row[2],
                        // Add more columns as needed
                    ]);
                }
            }
        }
            }
        } else {
            # code...
        }
    }
}
