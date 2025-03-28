<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name}';

    protected $description = '建立一個新的 Repository 類別';

    public function handle()
    {
        $name = $this->argument('name');

        if (! File::exists($path = app_path('Repositories'))) {
            File::makeDirectory($path);
        }

        if (! File::exists(app_path('Repositories/Repository.php'))) {
            File::put(
                app_path('Repositories/Repository.php'),
                $this->getRepositoryContent()
            );
        }

        // 建立特定的 Repository
        $repositoryContent = <<<EOT
<?php

namespace App\Repositories;

use App\Models\\{$name};

class {$name}Repository extends Repository
{
    public function __construct({$name} \$model)
    {
        parent::__construct(\$model);
    }
}
EOT;

        File::put(app_path("Repositories/{$name}Repository.php"), $repositoryContent);

        $this->info('Repository 建立成功！');
    }

    protected function getRepositoryContent()
    {
        // 回傳上面的 Repository 基礎類別內容
    }
}
