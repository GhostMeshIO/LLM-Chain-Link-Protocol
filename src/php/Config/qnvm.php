<?php

/**
 * QNVM Bridge Configuration – v0.4 with paradox keys.
 */

return [
    /*
     | Python binary path.
     */
    'python_bin' => env('QNVM_PYTHON_BIN', 'python3'),

    /*
     | Subprocess timeout (seconds).
     */
    'timeout' => (int) env('QNVM_TIMEOUT', 30),

    /*
     | Number of retry attempts on failure.
     */
    'max_retries' => (int) env('QNVM_MAX_RETRIES', 3),

    /*
     | Default repository path (optional).
     */
    'default_repo_path' => env('QNVM_REPO_PATH', null),

    /*
     | Debug mode (logs commands and outputs).
     */
    'debug' => env('QNVM_DEBUG', false),

    /*
     | Paradox Shield configuration.
     */
    'paradox' => [
        'shield_cap' => (float) env('PARADOX_SHIELD_CAP', 5.0),
        'pressure_threshold' => (float) env('PARADOX_PRESSURE_THRESHOLD', 6.0),
        'recharge_rate' => (float) env('PARADOX_RECHARGE_RATE', 0.03),
        'absorption_factor' => (float) env('PARADOX_ABSORPTION_FACTOR', 0.15),
    ],

    /*
     | DarkWisdom trickle charge phases.
     */
    'dark_wisdom' => [
        'phase1_end' => 15,
        'phase2_end' => 30,
        'phase1_budget' => 2.0,
        'phase2_budget' => 5.0,
        'phase3_base' => 5.0,
        'phase3_increment' => 0.1,
    ],
];
