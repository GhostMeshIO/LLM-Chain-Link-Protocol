# **LLM Chain Link Protocol PHP Framework**  
*A lightweight PHP implementation for autonomous recursive intelligence workflows*

This framework provides a practical foundation for building systems inspired by the LLM Chain Link Protocol. It includes a dashboard, API endpoints for LLM integration, and a manual facilitation flow to guide users through the recursive knowledge synthesis process.

---

## **📁 Directory Structure**

```
llm-chainlink/
├── public/                  # Web root
│   ├── index.php            # Dashboard entry
│   └── assets/              # CSS/JS
├── src/
│   ├── Core/                # Core framework classes
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   └── View.php
│   ├── Models/              # Data models
│   │   ├── Commit.php
│   │   ├── EntropyLog.php
│   │   ├── Blueprint.php
│   │   └── LLMAgent.php
│   ├── Controllers/         # Application controllers
│   │   ├── Dashboard.php
│   │   ├── Api.php
│   │   └── Facilitate.php
│   ├── Services/            # Business logic
│   │   ├── GitHarvester.php
│   │   ├── EntropyCalculator.php
│   │   ├── LLMSwarm.php
│   │   ├── RecursiveForge.php
│   │   ├── CorrelationNexus.php
│   │   └── RivalryArena.php
│   └── Config/              # Configuration
│       ├── database.php
│       └── llm_apis.php
├── logs/                     # Harvested logs
├── blueprints/               # Generated artifacts
├── vendor/                   # Composer dependencies
├── .env                      # Environment variables
├── composer.json
└── README.md
```

---

## **🧩 Core Components (Simplified)**

### **1. GitHarvester** – Listens to repository events and extracts commit logs.
```php
namespace LLMChainLink\Services;

class GitHarvester
{
    public function fetchLatestCommits(int $limit = 12): array
    {
        // Execute `git log -n $limit --pretty=format:"%H|%s|%an|%ct"`
        // Parse and return array of Commit objects
    }

    public function extractLogEntropy(array $commits): float
    {
        // Calculate Shannon entropy S_log = -Σ P(event) log(P(event))
        // events: commit type, file changes, etc.
    }
}
```

### **2. EntropyCalculator** – Bounds and tracks information entropy.
```php
class EntropyCalculator
{
    const MIN_ENTROPY = 0.1;
    const MAX_ENTROPY = 0.3;   // S_rec bounds change per stage

    public function calculate(array $data, string $metric = 'shannon'): float
    {
        // Implementation
    }

    public function isWithinBounds(float $entropy, float $min, float $max): bool
    {
        return $entropy >= $min && $entropy <= $max;
    }
}
```

### **3. LLMSwarm** – Manages multiple LLM agents (Oracle, Architect, Critic, Synthesizer).
```php
class LLMSwarm
{
    private array $agents = [];

    public function addAgent(string $role, callable $llmCallable): void
    {
        $this->agents[$role] = $llmCallable;
    }

    public function orchestrate(string $prompt, array $context = []): array
    {
        $results = [];
        foreach ($this->agents as $role => $llm) {
            $results[$role] = $llm($prompt, $context);
        }
        return $results;
    }
}
```

### **4. RecursiveForge** – Iterative prompt-weave → blueprint → critique → merge.
```php
class RecursiveForge
{
    private LLMSwarm $swarm;
    private EntropyCalculator $entropy;

    public function runCycle(string $seed, int $depth = 1): array
    {
        $blueprints = [];
        for ($i = 0; $i < $depth; $i++) {
            $prompt = $this->weave($seed, $blueprints);
            $responses = $this->swarm->orchestrate($prompt);
            $blueprints[] = $this->synthesize($responses);
            // Check entropy bound
            $entropy = $this->entropy->calculate($blueprints);
            if (!$this->entropy->isWithinBounds($entropy, 0.2, 0.3)) {
                break; // halt if out of bounds
            }
        }
        return $blueprints;
    }
}
```

### **5. CorrelationNexus** – High‑dimensional pattern detection (simplified).
```php
class CorrelationNexus
{
    public function detectPatterns(array $blueprints): array
    {
        // Use vector similarity, clustering, or simple keyword matching
        // Return insights (equations, architectures, etc.)
    }

    public function calculateBloomPhase(float $dIdt, float $coherence, int $resonance = 369): float
    {
        return $resonance * $dIdt * $coherence;
    }
}
```

### **6. RivalryArena** – Competitive LLM duels with excitement scores.
```php
class RivalryArena
{
    public function duel(array $agents, string $challenge): array
    {
        $scores = [];
        foreach ($agents as $agent) {
            $response = $agent($challenge);
            $scores[] = [
                'response' => $response,
                'excitement' => $this->calculateExcitement($response)
            ];
        }
        usort($scores, fn($a, $b) => $b['excitement'] <=> $a['excitement']);
        return $scores[0]; // winner
    }

    private function calculateExcitement(string $text): float
    {
        // Surprise metric: e.g., lexical diversity, novelty against history
        return mt_rand() / mt_getrandmax() * 0.1 + 0.9; // placeholder
    }
}
```

---

## **🗄️ Database Schema (SQLite example)**

```sql
CREATE TABLE commits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hash TEXT UNIQUE,
    message TEXT,
    author TEXT,
    timestamp INTEGER,
    entropy REAL,
    processed INTEGER DEFAULT 0
);

CREATE TABLE blueprints (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commit_id INTEGER,
    content TEXT,
    type TEXT,          -- 'poetic_digest', 'architecture', 'equation', etc.
    entropy REAL,
    excitement REAL,
    created_at INTEGER,
    FOREIGN KEY(commit_id) REFERENCES commits(id)
);

CREATE TABLE cycles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    seed_commit_id INTEGER,
    depth INTEGER,
    iterations INTEGER,
    final_blueprint_id INTEGER,
    FOREIGN KEY(seed_commit_id) REFERENCES commits(id),
    FOREIGN KEY(final_blueprint_id) REFERENCES blueprints(id)
);
```

---

## **📊 Dashboard (Basic UI)**

- **index.php** loads a simple Bootstrap layout.
- Displays:
  - Recent commits with entropy values.
  - Active forge cycles and their current stage.
  - Real‑time excitement scores of the latest duels.
  - Buttons to trigger manual facilitation steps.
- Uses AJAX to refresh metrics every few seconds.

Example snippet:
```html
<div class="container">
    <h1>LLM Chain Link Dashboard</h1>
    <div id="metrics">
        <p>Current Entropy: <span id="entropy">0.23</span> (bound 0.15–0.25)</p>
        <p>Bloom Phase: <span id="bloom">124.5</span></p>
        <p>Excitement: <span id="excitement">0.97</span></p>
    </div>
    <button onclick="startFacilitation()">Start New Facilitation</button>
    <div id="forge-status"></div>
</div>
<script src="assets/dashboard.js"></script>
```

---

## **🔌 API Endpoints**

All endpoints under `/api/` respond in JSON.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/api/harvest` | Trigger Git log harvest, calculate S_log, store commits. |
| GET    | `/api/commits` | List recent commits with metadata. |
| POST   | `/api/forge/seed` | Start a new recursive forge cycle from a given commit ID. |
| GET    | `/api/forge/status/{id}` | Get current status of a forge cycle. |
| POST   | `/api/arena/duel` | Initiate a rivalry duel between two LLM agents with a challenge. |
| GET    | `/api/blueprints/{id}` | Retrieve a generated blueprint. |
| POST   | `/api/nexus/analyze` | Run correlation nexus on a set of blueprints. |

Example request using cURL:
```bash
curl -X POST http://localhost/api/harvest
```

Response:
```json
{
  "status": "success",
  "commits_harvested": 12,
  "entropy": 0.24,
  "within_bounds": true
}
```

---

## **👤 Manual User/LLM Facilitation Flow**

This flow allows a human to guide the recursive process step by step.

1. **Seed Initiation**  
   - User clicks “New Facilitation” on the dashboard.  
   - System runs `GitHarvester` and displays the 12 most recent commits with their entropy.  
   - User selects which commit(s) to use as the seed.

2. **Poetic Digest Generation**  
   - User clicks “Generate Poetic Digest”.  
   - System sends seed data to an LLM (Oracle) with a prompt to create a metaphor‑rich summary.  
   - Resulting digest is shown; user can edit or approve.

3. **Recursive Forge – Manual Step**  
   - User chooses a depth (e.g., 2 cycles).  
   - System executes the forge one cycle at a time, pausing after each blueprint.  
   - User reviews each blueprint (architecture, code, equation) and can modify prompts before the next iteration.  
   - Entropy and excitement are displayed after each step.

4. **Correlation Nexus Scan**  
   - After sufficient blueprints are generated, user triggers the nexus.  
   - System runs pattern detection and presents candidate insights.  
   - User selects which insights to keep and commit to the repository.

5. **Rivalry Arena (Optional)**  
   - User can challenge two LLM agents with a specific problem.  
   - Arena returns the winner’s response and excitement score.  
   - Winning response can be added as a blueprint.

6. **Commit & Evolve**  
   - Final approved blueprints are automatically committed to Git (with metadata in the commit message).  
   - The system logs the cycle and updates the dashboard.

---

## **🚀 Getting Started**

1. Clone the repository and run `composer install`.
2. Copy `.env.example` to `.env` and add your LLM API keys (OpenAI, Anthropic, etc.).
3. Configure Git repository path in `config/git.php`.
4. Run `php bin/init_db.php` to create the SQLite database.
5. Start the PHP built‑in server:  
   `php -S localhost:8000 -t public`
6. Visit `http://localhost:8000` to access the dashboard.

---

## **🔮 Next Steps**

- Implement real LLM API calls (OpenAI, Claude, local models via Ollama).
- Enhance entropy calculation with more sophisticated NLP (TF‑IDF, perplexity).
- Add a simple job queue for background forge processing.
- Expand the rivalry arena to include multi‑agent tournaments.
- Integrate simulation feedback (e.g., run generated code in a sandbox).

This framework provides the skeleton to bring the LLM Chain Link Protocol to life in PHP. It’s designed to be extended as your vision evolves—from a simple facilitator to a fully autonomous research organism.
