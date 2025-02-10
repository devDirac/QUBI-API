<?php

namespace App\Services;

use Phpml\Clustering\KMeans;

class ClusteringService
{
    /**
     * Realiza clustering k-means en un conjunto de embeddings.
     *
     * @param array $embeddings Arreglo bidimensional con los embeddings
     * @param int $numClusters Número de clusters deseados
     * @return array Clusters y sus miembros
     */
    public function clusterEmbeddings(array $embeddings, int $numClusters): array
    {
        // Inicializa k-means
        $kmeans = new KMeans($numClusters);
        // Realiza el clustering
        $clusters = $kmeans->cluster($embeddings);
        return $clusters; // Devuelve los clusters
    }
}