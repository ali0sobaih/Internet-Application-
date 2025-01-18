<?php

namespace App\services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateProcessor
{
    public function process()
    {
        // Get all the tasks from update_tasks table
        $tasks = DB::table('update_tasks')->get();

        foreach ($tasks as $task) {
            try {
                // Normalize and compute the diff
                $normalizedOldFile = $this->normalizeContent("public/{$task->old_file}");
                $normalizedNewFile = $this->normalizeContent("public/{$task->new_file}");

                $diff = $this->myersDiff($normalizedOldFile, $normalizedNewFile);

                // Save diff in DB or as file
                if (strlen($diff) > 10000) {
                    $diffFileName = "diff_{$task->archive_id}_" . now()->timestamp . ".txt";
                    Storage::put("public/diffs/{$diffFileName}", $diff);
                    $difference = json_encode(['file' => $diffFileName]);
                } else {
                    $difference = $diff;
                }

                // Insert the diff into the updates table
                DB::table('updates')->insert([
                    'user_id' => $task->user_id,
                    'archive_id' => $task->archive_id,
                    'difference' => $difference,
                    'created_at' => now(),
                ]);

                Log::info("Processed task ID: {$task->id}");

                // Remove the processed task from update_tasks table
                DB::table('update_tasks')->where('id', $task->id)->delete();
            } catch (\Exception $e) {
                Log::error("Failed to process update task ID: {$task->id}", ['error' => $e->getMessage()]);
            }
        }
    }

    private function normalizeContent(string $filePath): string
    {
        if (!Storage::exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $content = Storage::get($filePath);

        // Normalize content by trimming spaces and collapsing multiple spaces into one
        $lines = explode("\n", $content);
        $normalizedLines = array_map(function ($line) {
            return preg_replace('/\s+/', ' ', trim($line));
        }, $lines);

        return implode("\n", array_filter($normalizedLines, fn($line) => $line !== ''));
    }

    private function myersDiff(string $oldContent, string $newContent): string
    {
        $oldLines = explode("\n", $oldContent);
        $newLines = explode("\n", $newContent);

        $diff = $this->myersAlgorithm($oldLines, $newLines);
        return "--- Original\n+++ New\n" . $this->formatDiff($diff);
    }

    private function myersAlgorithm(array $oldLines, array $newLines): array
    {
        // Implementation of the Myers Diff algorithm
        $diff = [];
        $m = count($oldLines);
        $n = count($newLines);
        $d = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));

        // Populate the diff matrix
        for ($i = 0; $i <= $m; $i++) {
            for ($j = 0; $j <= $n; $j++) {
                if ($i == 0 || $j == 0) {
                    $d[$i][$j] = 0;
                } elseif ($oldLines[$i - 1] == $newLines[$j - 1]) {
                    $d[$i][$j] = $d[$i - 1][$j - 1] + 1;
                } else {
                    $d[$i][$j] = max($d[$i - 1][$j], $d[$i][$j - 1]);
                }
            }
        }

        // Backtrack to find the diff
        $i = $m;
        $j = $n;
        while ($i > 0 && $j > 0) {
            if ($oldLines[$i - 1] == $newLines[$j - 1]) {
                $diff[] = " " . $oldLines[$i - 1];
                $i--;
                $j--;
            } elseif ($d[$i - 1][$j] >= $d[$i][$j - 1]) {
                $diff[] = "- " . $oldLines[$i - 1];
                $i--;
            } else {
                $diff[] = "+ " . $newLines[$j - 1];
                $j--;
            }
        }

        // Add remaining lines
        while ($i > 0) {
            $diff[] = "- " . $oldLines[$i - 1];
            $i--;
        }
        while ($j > 0) {
            $diff[] = "+ " . $newLines[$j - 1];
            $j--;
        }

        return array_reverse($diff);
    }

    private function formatDiff(array $diff): string
    {
        return implode("\n", $diff);
    }
}
