<?php

/**
 * QNVM Bridge Configuration.
 *
 * Return an array of settings for the QNVMBridge service.
 */

return [
    /*
     |--------------------------------------------------------------------------
     | Python Binary Path
     |--------------------------------------------------------------------------
     |
     | Specify the path to the Python interpreter that should be used to run
     | the QNVM simulation. This can be an absolute path or just 'python3'
     | if it's in the system PATH.
     |
     */
    'python_bin' => env('QNVM_PYTHON_BIN', 'python3'),

    /*
     |--------------------------------------------------------------------------
     | Subprocess Timeout (seconds)
     |--------------------------------------------------------------------------
     |
     | Maximum number of seconds to wait for the QNVM subprocess to complete.
     | If the simulation takes longer, the process will be terminated.
     |
     */
    'timeout' => (int) env('QNVM_TIMEOUT', 30),

    /*
     |--------------------------------------------------------------------------
     | Default Repository Path
     |--------------------------------------------------------------------------
     |
     | Optional fallback repository path if none is provided at runtime.
     | This is primarily used for testing or CLI scripts.
     |
     */
    'default_repo_path' => env('QNVM_REPO_PATH', null),

    /*
     |--------------------------------------------------------------------------
     | Debug Mode
     |--------------------------------------------------------------------------
     |
     | When true, the bridge will log the full command and raw output.
     |
     */
    'debug' => env('QNVM_DEBUG', false),
];
