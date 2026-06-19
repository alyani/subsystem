<?php

namespace Alyani\Subsystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateDataTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dataTable {filename? : The name of the file to create.}';
    protected string $fileName;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dataTable.';

    /**
     * Execute the console command.
     */
    //

    public function handle()
    {
        $directoryPath = base_path() . "/app/DataTables/";

        // Check the filename exists or not. (if not, it will return a failure)
        if (!$this->argument('filename')) {
            $input = $this->ask('What is the name of dataTable? e.g. UserDataTable');
            if (!$input) {
                $this->error('The dataTable name cannot be empty.');
                return Command::FAILURE;
            }
            $this->fileName = pathinfo($input, PATHINFO_FILENAME);
        } else {
            $this->fileName = pathinfo($this->argument('filename'), PATHINFO_FILENAME);
        }

        $filePath = $directoryPath . $this->fileName . ".php";

        // Check the destination folder exists on not. (if not, it will create.)
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // Check is the file exists on destination path or not. (if existed, it will return a failure)
        if (file_exists($filePath)) {
            $this->error("The file '{$this->fileName}.php' already exists.");
            return Command::FAILURE;
        }

        // Create the dataTable
        File::put($filePath, $this->getDataTableTemplate());

        $this->info("DataTable [{$filePath}] created successfully.");
        return Command::SUCCESS;
    }

    /*
     * Return a dataTable template based on model
     */
    public function getDataTableTemplate(): string
    {
        // Extract the model name
        preg_match('/(\w+)(?=DataTable)/', $this->fileName, $matches);
        $modelName = ucfirst(($matches[1] ?? ''));

        return "<?php\n\nnamespace App\\DataTables;\n\nuse Illuminate\\Database\\Eloquent\\Builder as QueryBuilder;\nuse Alyani\\Subsystem\\DataTables\\DataTable;\nuse Yajra\\DataTables\\EloquentDataTable;\nuse Yajra\\DataTables\\Html\\Column;\nuse App\\Models\\{$modelName};\n\nclass $this->fileName extends DataTable\n{\n    /**\n     * Build the DataTable class.\n     *\n     * @param QueryBuilder \$query Results from query() method.\n     */\n    public function dataTable(QueryBuilder \$query): EloquentDataTable\n    {\n        return (new EloquentDataTable(\$query))\n            ->filter(function (\$query) {\n                return \$query;\n            })\n//            ->addColumn('name#1', function (\$model) {\n//            })\n//            ->editColumn('name#2', function (\$model) {\n//            })\n//            ->rawColumns([])\n            ->setTotalRecords(\$query->count())\n            ->addIndexColumn()\n            ->orderColumn('ID', ':column \$1')\n            ->setRowId('ID');\n    }\n\n    /**\n     * Get the query source of dataTable.\n     */\n    public function query({$modelName} \$model): QueryBuilder\n    {\n        return \$model->newQuery();\n    }\n\n    /**\n     * Get the dataTable columns definition.\n     */\n    public function getColumns(): array\n    {\n        return [\n            Column::make('DT_RowIndex')->title('#')->orderable(false),\n        ];\n    }\n}\n";
    }
}
