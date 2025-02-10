<?php

namespace App\Services;

class SimilarityService
{
    /**
     * Calcula similitudes dentro de un cluster de embeddings.
     */
    public function calculateSimilaritiesWithinCluster(array $cluster): array
    {
        $similarities = [];
        for ($i = 0; $i < count($cluster); $i++) {
            for ($j = $i + 1; $j < count($cluster); $j++) {
                $similarities[] = [
                    'pair' => [$i, $j],
                    'similarity' => $this->cosineSimilarity($cluster[$i], $cluster[$j]),
                ];
            }
        }
        return $similarities;
    }

    /**
     * Calcula similitud de coseno entre dos vectores.
     */
    public function cosineSimilarity(array $embedding1, array $embedding2): float
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        for ($i = 0; $i < count($embedding1); $i++) {
            $dotProduct += $embedding1[$i] * $embedding2[$i];
            $magnitude1 += pow($embedding1[$i], 2);
            $magnitude2 += pow($embedding2[$i], 2);
        }
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}