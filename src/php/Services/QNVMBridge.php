<?php

namespace GhostMesh\Services;

/**
 * QNVMBridge – Handles subprocess communication with the Python QNVM runner.
 *
 * v0.3: Executes `python/qnvm/runner.py` with repo path and s_log,
 *       captures JSON output, and returns decoded entities + Dark Wisdom.
 */
class QNVMBridge
{
    /**
     * @var string|null Last raw output from the subprocess (for debugging).
     */
    private ?string $lastOutput = null;

    /**
     * @var string|null Last error output from the subprocess.
     */
    private ?string $lastError = null;

    /**
     * @var array Configuration from config/qnvm.php.
     */
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Run the QNVM simulation as a subprocess.
     *
     * @param string $repoPath Absolute path to the Git repository.
     * @param float $s_log Current Shannon entropy value.
     * @return array Decoded JSON result with keys:
     *               - 'entities' (array) List of generated entities.
     *               - 'dark_wisdom' (array) Dark Wisdom metrics.
     * @throws \RuntimeException If the subprocess fails or returns invalid JSON.
     */
    public function runSimulation(string $repoPath, float $s_log): array
    {
        // Locate the Python runner script (relative to project root).
        $runnerScript = realpath(__DIR__ . '/../../../python/qnvm/runner.py');
        if (!$runnerScript || !is_file($runnerScript)) {
            throw new \RuntimeException("QNVM runner not found at: $runnerScript");
        }

        // Build command with optional Python path from config.
        $pythonBin = $this->config['python_bin'] ?? 'python3';
        $command = sprintf(
            '%s %s --repo-path %s --s-log %s',
            escapeshellarg($pythonBin),
            escapeshellarg($runnerScript),
            escapeshellarg($repoPath),
            escapeshellarg((string)$s_log)
        );

        // Set up descriptors for proc_open.
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException("Failed to start subprocess: $command");
        }

        // Close stdin immediately (no input needed).
        fclose($pipes[0]);

        // Set streams non‑blocking for timeout handling.
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output = '';
        $error = '';
        $startTime = time();
        $timeout = $this->config['timeout'] ?? 30;

        // Read output until timeout or process finishes.
        while (true) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }

            if (time() - $startTime > $timeout) {
                proc_terminate($process);
                throw new \RuntimeException("Subprocess timed out after {$timeout} seconds.");
            }

            // Read available data.
            $out = fread($pipes[1], 8192);
            if ($out !== false) {
                $output .= $out;
            }
            $err = fread($pipes[2], 8192);
            if ($err !== false) {
                $error .= $err;
            }

            usleep(10000); // 10ms
        }

        // Close pipes.
        fclose($pipes[1]);
        fclose($pipes[2]);

        // Get final return code.
        $returnCode = proc_close($process);

        $this->lastOutput = $output;
        $this->lastError = $error;

        if ($returnCode !== 0) {
            throw new \RuntimeException(
                "Subprocess failed with code $returnCode.\nStderr: $error\nStdout: $output"
            );
        }

        // Parse JSON output.
        $decoded = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                "Invalid JSON from subprocess: " . json_last_error_msg() . "\nOutput: $output"
            );
        }

        return $decoded;
    }

    /**
     * Get the last raw stdout output.
     */
    public function getLastOutput(): ?string
    {
        return $this->lastOutput;
    }

    /**
     * Get the last raw stderr output.
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
