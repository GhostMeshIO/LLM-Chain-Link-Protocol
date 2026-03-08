#!/usr/bin/env python3
"""
QNVM Runner – Live entropy integration.

v0.4: Uses the received s_log to influence simulation parameters (e.g., founder entropy,
      drift scaling). Runs one generation and outputs entities + DarkWisdom.
"""

import argparse
import json
import sys
import os
import random

# Add parent directory to path so we can import core modules.
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

# Import QNVM core
from qnvm.core.universe import Universe
from qnvm.core.wisdom import DarkWisdomExtractor
from qnvm.core.constants import S3_DARK_WISDOM, S3_ENTITY_IQ, S3_ENTITY_SOPHIA, \
    S3_ENTITY_COHERENCE, S3_ENTITY_ENTROPY, S3_EXTINCT_LINEAGES


def main():
    parser = argparse.ArgumentParser(description="QNVM Runner with live entropy")
    parser.add_argument("--repo-path", required=True, help="Path to Git repository (unused)")
    parser.add_argument("--s-log", type=float, required=True, help="Current Shannon entropy")
    args = parser.parse_args()

    # Seed RNG with s_log to make simulations reproducible but entropy‑dependent.
    random.seed(int(args.s_log * 10000))

    # Initialize DarkWisdom from Stage 3 snapshot.
    dw_extractor = DarkWisdomExtractor(
        payload=S3_DARK_WISDOM,
        source_iq=S3_ENTITY_IQ,
        source_sophia=S3_ENTITY_SOPHIA,
        source_coherence=S3_ENTITY_COHERENCE,
        source_entropy=S3_ENTITY_ENTROPY,
        extinct_lineages=S3_EXTINCT_LINEAGES
    )

    # Use s_log to scale initial entropy of new entities.
    # Map s_log from [0.1, 0.3] to [0.1, 0.4] for entropy_base scaling.
    entropy_scale = 0.1 + (args.s_log * 0.3)  # Example mapping

    universe = Universe(
        name="QNVM-Live",
        dw_extractor=dw_extractor,
        initial_size=12,  # 12 commits -> 12 entities
        genesis_mode=False,
        entropy_scale=entropy_scale,  # Pass to universe (modify Universe __init__ accordingly)
        seed=None
    )

    # Run one generation.
    universe.step()

    # Extract entities (full profiles? limited for brevity).
    entities = []
    for e in universe.entities[:7]:  # Top 7 by Sophia
        entities.append({
            "id": e.id,
            "archetype": e.archetype,
            "sophia_score": round(e.sophia_score, 4),
            "recursive_depth": e.recursive_depth,
            "sovereign": e.sovereign_entity,
            "traits": e.traits[:3]  # First 3 traits
        })

    dark_wisdom = {
        "payload": dw_extractor.payload,
        "released": universe.dark_wisdom_released,
        "remaining": universe.trickle.remaining,
        "phase": universe.trickle._phase(universe.generation),
        "paradox_shards": dw_extractor.segments["DW_Paradox"]["paradox_shards"],
        "karma_nodes": dw_extractor.segments["DW_Karma"]["karma_nodes"]
    }

    output = {
        "entities": entities,
        "dark_wisdom": dark_wisdom,
        "generation": universe.generation,
        "paradox_pressure": universe.paradox_pressure,
        "avg_sophia": round(sum(e.sophia_score for e in universe.entities) / len(universe.entities), 4)
    }

    print(json.dumps(output, indent=2))


if __name__ == "__main__":
    main()
