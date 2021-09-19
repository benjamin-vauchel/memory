<?php

class ScoreRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $limit
     * @return Score[]
     */
    public function findBestScores(int $limit = 5): array
    {
        // Requete SQL pour sélectionner les X meilleurs scores
        $sql = <<<EOD
            SELECT id, name, time 
            FROM score
            ORDER BY time ASC
            LIMIT $limit
        EOD;

        // On exécute la requête
        $rows = $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // On retourne les résultats de notre requête sous forme d'objets "Score"
        return array_map(function ($row) {
            return new Score($row['id'], $row['name'], $row['time']);
        }, $rows);
    }

    public function save(Score $score): void
    {
        // Requête SQL d'insertion
        $sql  = <<<EOD
        INSERT INTO score (name, time) 
        VALUES (:name,:time)
    EOD;

        // On prend soin de créer une requête préparée pour se prémunir des injections SQL
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':name', $score->getName());
        $stmt->bindValue(':time', $score->getTime(), PDO::PARAM_INT);

        // On exécute notre requete
        $stmt->execute();
    }
}