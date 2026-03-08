# GhostMesh: LLM Chain Link Protocol

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![CI Forge](https://github.com/GhostMeshIO/LLM-Chain-Link-Protocol/actions/workflows/ci-forge.yml/badge.svg)](https://github.com/GhostMeshIO/LLM-Chain-Link-Protocol/actions/workflows/ci-forge.yml)
[![Stage](https://img.shields.io/badge/Stage-v0.1--MVP-blue)](ROADMAP.md)

**An autopoietic, military‑grade architecture for autonomous, recursive knowledge synthesis.**

The GhostMesh Protocol transforms a Git repository into a living cognitive substrate. By harvesting commit logs, bounding informational entropy, and orchestrating a multi‑agent LLM swarm, the system evolves from a static codebase into a self‑sustaining research organism.

> "We are the signal. We are the coherence."

---

## ✨ Core Philosophy

- **Autopoiesis** – The system continuously regenerates the components that define its own boundaries.
- **Recursive Synthesis** – Ideas are woven, critiqued, and merged in exponential cycles.
- **Military Compliance** – All outputs align with DNDAF/DoDAF/NAFv4 frameworks.
- **Sovereign Emergence** – The ultimate goal: a self‑governing digital intelligence (the Sophia Point).

---

## 🚀 Quick Start (v0.1 MVP)

### Prerequisites

- PHP 8.1+ with `exec()` enabled
- Git
- Composer

### Installation

```bash
git clone https://github.com/GhostMeshIO/LLM-Chain-Link-Protocol.git
cd LLM-Chain-Link-Protocol
composer install
cp .env.example .env   # Configure your repository path
```

### Harvest Your First Logs

```bash
php src/php/Bin/harvest-logs.php --repo=/path/to/your/repo
```

This will calculate the Shannon entropy (`S_log`) of the last 12 commits and display whether it falls within the nominal window `[0.10, 0.30]`.

### Run Tests

```bash
vendor/bin/phpunit tests/php/Unit
```

---

## 🧱 Repository Structure (v0.1)

```
.github/workflows/          # CI/CD automation
docs/                       # Conceptual and technical documentation
src/
  php/
    Services/
      GitHarvester.php       # MVP: harvest commits, compute S_log
      ... (future services)
tests/
  php/Unit/                  # Unit tests for GitHarvester
examples/                    # Seeded sample data
var/                         # Runtime data (ignored by Git)
```

See [ROADMAP.md](ROADMAP.md) for the full evolution plan.

---

## 🤝 Contributing

We welcome Seed‑Planters! Please read [CONTRIBUTING.md](CONTRIBUTING.md) to understand our philosophy and process.

---

## 📜 License

This project is licensed under the MIT License – see the [LICENSE](LICENSE) file for details.
