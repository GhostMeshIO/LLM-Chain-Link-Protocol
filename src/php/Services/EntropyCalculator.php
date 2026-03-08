<?php

namespace GhostMesh\Services;

/**
 * EntropyCalculator – Computes Shannon entropy from commit data and checks bounds.
 *
 * v0.3: Pure logic separated from GitHarvester for reusability.
 */
class EntropyCalculator
{
    /**
     * Minimum allowed entropy for nominal operation.
     */
    public const MIN_ENTROPY = 0.10;

    /**
     * Maximum allowed entropy for nominal operation.
     */
    public const MAX_ENTROPY = 0.30;

    /**
     * Compute Shannon entropy based on commit message types.
     *
     * @param array $commits List of commit arrays, each with a 'message' key.
     * @return float Entropy value between 0 and log2(N).
     */
    public function compute(array $commits): float
    {
        if (empty($commits)) {
            return 0.0;
        }

        $eventTypes = [];
        foreach ($commits as $commit) {
            $msg = $commit['message'];
            // Extract conventional commit type (e.g., feat:, fix:, docs:)
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
     * Check if entropy is within the nominal operating window.
     *
     * @param float $entropy
     * @return bool
     */
    public function isWithinBounds(float $entropy): bool
    {
        return $entropy >= self::MIN_ENTROPY && $entropy <= self::MAX_ENTROPY;
    }
}
