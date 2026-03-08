<?php

namespace GhostMesh\Services;

/**
 * GitHarvester – Fetches commit logs, calculates Shannon entropy (S_log),
 * and integrates with the 3‑6‑9 resonance scheduler.
 *
 * v0.2 – Now generation‑aware and provides hooks for the Paradox Shield.
 */
class GitHarvester
{
    /**
     * @var int Current generation number (auto‑increments each harvest cycle).
     */
    private int $generation = 0;

    /**
     * @var float Most recently computed entropy value (S_log).
     */
    private float $lastSlog = 0.0;

    /**
     * Harvest commits from a given repository path.
     *
     * @param string $repoPath Absolute path to the Git repository.
     * @param int $limit Number of recent commits to harvest (default: 12).
     * @return array Associative array with keys:
     *               - 's_log' (float) Shannon entropy value.
     *               - 'commits' (array) List of parsed commit data.
     *               - 'within_bounds' (bool) Whether S_log is within [0.10, 0.30].
     *               - 'resonance_mode' (bool) True if generation is divisible by 3, 6, or 9.
     *               - 'generation' (int) Current generation after harvest.
     */
    public function harvest(string $repoPath, int $limit = 12): array
    {
        // Increment generation on each harvest call
        $this->generation++;

        // Validate repository path
        if (!is_dir($repoPath) || !is_dir($repoPath . '/.git')) {
            throw new \InvalidArgumentException("Invalid Git repository path: $repoPath");
        }

        // Execute git log command
        $command = sprintf(
            'git -C %s log -n %d --pretty=format:"%%H|%%s|%%an|%%ct"',
            escapeshellarg($repoPath),
            $limit
        );
        exec($command, $outputLines, $returnCode);

        if ($returnCode !== 0 || empty($outputLines)) {
            return $this->buildResult([], 0.0, false);
        }

        // Parse each line into structured commit data
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

        // Compute Shannon entropy
        $s_log = $this->computeEntropy($commits);
        $this->lastSlog = $s_log;

        // Determine if resonance mode is active
        $resonance = $this->isResonanceMode($this->generation);

        // Build result with resonance flag
        return [
            's_log'          => round($s_log, 4),
            'commits'        => $commits,
            'within_bounds'  => ($s_log >= 0.10 && $s_log <= 0.30),
            'resonance_mode' => $resonance,
            'generation'     => $this->generation,
        ];
    }

    /**
     * Compute Shannon entropy based on commit message types.
     * (Simplified for v0.2 – can be expanded later.)
     *
     * @param array $commits List of parsed commit data.
     * @return float Entropy value between 0 and log2(N).
     */
    private function computeEntropy(array $commits): float
    {
        if (empty($commits)) {
            return 0.0;
        }

        $eventTypes = [];
        foreach ($commits as $commit) {
            $msg = $commit['message'];
            if (preg_match('/^([a-zA-Z]+)[: ]/', $msg, $matches)) {
                $type = strtolower($matches[1]);
            } else {
                $type = 'other';
            }
            $eventTypes[] = $type;
        }

        $counts = array_count_values($eventTypes);
        $total = count($eventTypes);
        $entropy = 0.0;

        foreach ($counts as $count) {
            $p = $count / $total;
            $entropy -= $p * log($p, 2);
        }

        return $entropy;
    }

    /**
     * Check if a generation number activates the 3‑6‑9 resonance mode.
     *
     * @param int $generation
     * @return bool True if generation % 3 == 0 or % 6 == 0 or % 9 == 0.
     */
    private function isResonanceMode(int $generation): bool
    {
        return ($generation % 3 === 0) || ($generation % 6 === 0) || ($generation % 9 === 0);
    }

    /**
     * Get the current resonance mode without performing a harvest.
     *
     * @return bool
     */
    public function currentResonanceMode(): bool
    {
        return $this->isResonanceMode($this->generation);
    }

    /**
     * Placeholder for Paradox Shield integration.
     *
     * In future versions, this will communicate with the QNVM bridge
     * to adjust the shield charge based on entropy and resonance.
     *
     * @return array Stub response.
     */
    public function checkParadoxShield(): array
    {
        // TODO: Implement actual Paradox Shield logic via QNVMBridge.
        return [
            'shield_charge' => 5.0,
            'absorbed'      => 0.0,
            'metabolized'   => 0,
        ];
    }

    /**
     * Build a minimal result array for error cases.
     */
    private function buildResult(array $commits, float $s_log, bool $within_bounds): array
    {
        return [
            's_log'          => round($s_log, 4),
            'commits'        => $commits,
            'within_bounds'  => $within_bounds,
            'resonance_mode' => $this->isResonanceMode($this->generation),
            'generation'     => $this->generation,
        ];
    }
}
