<?php

namespace GhostMesh\Services;

use GhostMesh\Config\Config;

/**
 * QNVMBridge – Full implementation with subprocess execution, logging, retries,
 * and DarkWisdom extraction.
 *
 * v0.4: Enhanced error handling, configurable retries, and direct DarkWisdom access.
 */
class QNVMBridge
{
    private string $pythonBin;
    private string $runnerScript;
    private int $timeout;
    private int $maxRetries;
    private bool $debug;
    private ?string $lastOutput = null;
    private ?string $lastError = null;

    public function __construct(array $config)
    {
        $this->pythonBin = $config['python_bin'] ?? 'python3';
        $this->runnerScript = realpath(__DIR__ . '/../../../python/qnvm/runner.py');
        if (!$this->runnerScript || !is_file($this->runnerScript)) {
            throw new \RuntimeException("QNVM runner not found at expected location.");
        }
        $this->timeout = $config['timeout'] ?? 30;
        $this->maxRetries = $config['max_retries'] ?? 3;
        $this->debug = $config['debug'] ?? false;
    }

    /**
     * Run the QNVM simulation, with retries on failure.
     *
     * @param string $repoPath
     * @param float $s_log
     * @return array
     */
    public function runSimulation(string $repoPath, float $s_log): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                return $this->execute($repoPath, $s_log);
            } catch (\RuntimeException $e) {
                $lastException = $e;
                $attempt++;
                if ($this->debug) {
                    error_log("QNVMBridge attempt $attempt failed: " . $e->getMessage());
                }
                usleep(100000 * $attempt); // Exponential backoff: 100ms, 200ms, ...
            }
        }

        throw new \RuntimeException(
            "QNVMBridge failed after {$this->maxRetries} attempts. Last error: " . $lastException->getMessage()
        );
    }

    /**
     * Execute the subprocess once.
     */
    private function execute(string $repoPath, float $s_log): array
    {
        $command = sprintf(
            '%s %s --repo-path %s --s-log %s',
            escapeshellarg($this->pythonBin),
            escapeshellarg($this->runnerScript),
            escapeshellarg($repoPath),
            escapeshellarg((string)$s_log)
        );

        if ($this->debug) {
            error_log("QNVMBridge executing: $command");
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException("Failed to start subprocess.");
        }

        fclose($pipes[0]); // no input

        // Set non-blocking for timeout
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output = '';
        $error = '';
        $start = time();

        while (true) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }

            if (time() - $start > $this->timeout) {
                proc_terminate($process);
                throw new \RuntimeException("Subprocess timed out after {$this->timeout}s.");
            }

            $out = fread($pipes[1], 8192);
            if ($out !== false) {
                $output .= $out;
            }
            $err = fread($pipes[2], 8192);
            if ($err !== false) {
                $error .= $err;
            }
            usleep(10000);
        }

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        $this->lastOutput = $output;
        $this->lastError = $error;

        if ($returnCode !== 0) {
            throw new \RuntimeException(
                "Subprocess failed with code $returnCode.\nStderr: $error\nStdout: $output"
            );
        }

        $decoded = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                "Invalid JSON from subprocess: " . json_last_error_msg() . "\nOutput: $output"
            );
        }

        return $decoded;
    }

    /**
     * Extract only DarkWisdom metrics from the simulation.
     */
    public function getDarkWisdom(string $repoPath, float $s_log): array
    {
        $result = $this->runSimulation($repoPath, $s_log);
        return $result['dark_wisdom'] ?? [];
    }

    /**
     * Get last raw output.
     */
    public function getLastOutput(): ?string
    {
        return $this->lastOutput;
    }

    /**
     * Get last error.
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
