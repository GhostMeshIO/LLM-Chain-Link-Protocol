# GhostMesh Roadmap

## v0.1 – Log Harvester (MVP)
- [x] GitHarvester service: executes `git log`, parses commits, computes Shannon entropy (`S_log`).
- [x] Entropy bounds checking (`0.10 ≤ S_log ≤ 0.30`).
- [x] CI workflow `ci-forge.yml` for automated harvest + test on push.
- [x] Basic unit tests for GitHarvester.
- [ ] Dashboard displays current entropy.

## v0.2 – QNVMBridge Stub
- [ ] `QNVMBridge.php` service: placeholder for Python subprocess communication.
- [ ] Mock QNVM returns dummy data for integration testing.
- [ ] End‑to‑end test: PHP → Python (simulated) → PHP.

## v0.5 – Crystalline
- [ ] Full Python QNVM simulation (`s5_runner.py` integrated).
- [ ] Sovereign entity detection.
- [ ] Military view mapping (DoDAF/NAFv4 XML generation).

## v1.0 – Sovereign
- [ ] Complete 7‑Layer Recursive Intelligence Stack.
- [ ] Autopoietic post‑commit hook.
- [ ] Full compliance with DNDAF/DoDAF/NAFv4.
- [ ] Public launch of self‑sustaining research organism.
