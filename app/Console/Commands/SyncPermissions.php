<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncPermissions extends Command
{
    protected $signature   = 'permissions:sync';
    protected $description = 'Sync permissions from config/permissions.php into the database';

    public function handle(): int
    {
        $registry = config('permissions', []);

        if (empty($registry)) {
            $this->warn('config/permissions.php is empty or missing.');
            return self::FAILURE;
        }

        $created = 0;
        $skipped = 0;

        foreach ($registry as $group => $actions) {
            foreach ($actions as $action) {
                $name        = "{$group}.{$action}";
                $displayName = Str::title(str_replace('_', ' ', $group))
                    . ' — '
                    . Str::title($action);

                $exists = Permission::where('name', $name)->exists();

                if ($exists) {
                    $this->line("  <fg=gray>— Skipped (exists):</> {$name}");
                    $skipped++;
                } else {
                    Permission::create([
                        'name'         => $name,
                        'display_name' => $displayName,
                        'group'        => $group,
                    ]);
                    $this->line("  <fg=green>✓ Created:</> {$name}");
                    $created++;
                }
            }
        }

        $this->newLine();
        $this->info("Done. {$created} new permission(s) added, {$skipped} skipped.");

        return self::SUCCESS;
    }
}
