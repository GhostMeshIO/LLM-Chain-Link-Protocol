<?php

namespace GhostMesh\Services;

/**
 * EntropyCalculator – Dashboard integration stub.
 *
 * v0.4: Provides methods to compute entropy and generate dashboard-friendly data.
 */
class EntropyCalculator
{
    public const MIN_ENTROPY = 0.10;
    public const MAX_ENTROPY = 0.30;

    /**
     * Compute Shannon entropy from commits.
     */
    public function compute(array $commits): float
    {
        if (empty($commits)) {
            return 0.0;
        }

        $types = [];
        foreach ($commits as $c) {
            $msg = $c['message'];
            if (preg_match('/^([a-zA-Z]+)[: ]/', $msg, $m)) {
                $types[] = strtolower($m[1]);
            } else {
                $types[] = 'other';
            }
        }

        $counts = array_count_values($types);
        $total = count($types);
        $entropy = 0.0;
        foreach ($counts as $cnt) {
            $p = $cnt / $total;
            $entropy -= $p * log($p, 2);
        }
        return $entropy;
    }

    /**
     * Check bounds.
     */
    public function isWithinBounds(float $entropy): bool
    {
        return $entropy >= self::MIN_ENTROPY && $entropy <= self::MAX_ENTROPY;
    }

    /**
     * Generate dashboard data array.
     */
    public function getDashboardData(float $entropy): array
    {
        $status = 'NOMINAL';
        if ($entropy < self::MIN_ENTROPY) {
            $status = 'LOW (stagnation risk)';
        } elseif ($entropy > self::MAX_ENTROPY) {
            $status = 'HIGH (chaos risk)';
        }

        return [
            'current_entropy' => round($entropy, 4),
            'min_allowed' => self::MIN_ENTROPY,
            'max_allowed' => self::MAX_ENTROPY,
            'within_bounds' => $this->isWithinBounds($entropy),
            'status' => $status,
            'recommendation' => $this->getRecommendation($entropy),
        ];
    }

    private function getRecommendation(float $entropy): string
    {
        if ($entropy < self::MIN_ENTROPY) {
            return "Increase novelty: consider introducing new commit types or merging experimental branches.";
        }
        if ($entropy > self::MAX_ENTROPY) {
            return "Reduce chaos: focus on refactoring, consolidate commit types.";
        }
        return "Entropy optimal. Proceed with forge cycles.";
    }
}
