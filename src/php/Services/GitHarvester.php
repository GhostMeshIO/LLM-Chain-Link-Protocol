<?php

namespace GhostMesh\Services;

/**
 * GitHarvester – Fetches commit logs and calculates Shannon entropy (S_log).
 *
 * v0.1 MVP: Executes `git log`, parses commits, computes entropy over event types.
 * Event types: commit type (feat/fix/docs/etc.), author, hour band, file count bucket.
 */
class GitHarvester
{
    /**
     * Harvest commits from a given repository path.
     *
     * @param string $repoPath Absolute path to the Git repository.
     * @param int $limit Number of recent commits to harvest (default: 12).
     * @return array Associative array with keys:
     *               - 's_log' (float) Shannon entropy value.
     *               - 'commits' (array) List of parsed commit data.
     *               - 'within_bounds' (bool) Whether S_log is within [0.10, 0.30].
     */
    public function harvest(string $repoPath, int $limit = 12): array
    {
        // Ensure the path is a valid Git repository
        if (!is_dir($repoPath) || !is_dir($repoPath . '/.git')) {
            throw new \InvalidArgumentException("Invalid Git repository path: $repoPath");
        }

        // Build and execute git log command
        $command = sprintf(
            'git -C %s log -n %d --pretty=format:"%%H|%%s|%%an|%%ct"',
            escapeshellarg($repoPath),
            $limit
        );
        exec($command, $outputLines, $returnCode);

        if ($returnCode !== 0 || empty($outputLines)) {
            return ['s_log' => 0.0, 'commits' => [], 'within_bounds' => false];
        }

        // Parse each line into a structured commit object
        $commits = [];
        foreach ($outputLines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 4) {
                $commits[] = [
                    'hash'      => $parts[0],
                    'message'   => $parts[1],
                    'author'    => $parts[2],
                    'timestamp' => (int)$parts[3],
                ];
            }
        }

        // Compute Shannon entropy over event types
        $s_log = $this->computeEntropy($commits);

        return [
            's_log'         => round($s_log, 4),
            'commits'       => $commits,
            'within_bounds' => ($s_log >= 0.10 && $s_log <= 0.30),
        ];
    }

    /**
     * Compute Shannon entropy based on commit message types.
     *
     * In a full implementation, this would also consider author, hour band,
     * file change counts, etc. For v0.1, we simplify to message prefix analysis.
     *
     * @param array $commits List of parsed commit data.
     * @return float Entropy value between 0 and log2(N).
     */
    private function computeEntropy(array $commits): float
    {
        if (empty($commits)) {
            return 0.0;
        }

        // Extract event types from commit message prefixes (e.g., "feat:", "fix:")
        $eventTypes = [];
        foreach ($commits as $commit) {
            $msg = $commit['message'];
            // Simple heuristic: first token before ':' or space
            if (preg_match('/^([a-zA-Z]+)[: ]/', $msg, $matches)) {
                $type = strtolower($matches[1]);
            } else {
                $type = 'other';
            }
            $eventTypes[] = $type;
        }

        // Count occurrences of each event type
        $counts = array_count_values($eventTypes);
        $total = count($eventTypes);

        // Calculate Shannon entropy: -Σ p * log2(p)
        $entropy = 0.0;
        foreach ($counts as $count) {
            $p = $count / $total;
            $entropy -= $p * log($p, 2);
        }

        return $entropy;
    }
}
