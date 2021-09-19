<?php

class Game
{
    public array $cards;

    public function __construct(int $pairs = 18)
    {
        // Créons les cartes de notre jeu de Memory
        // Comme il existe 18 symboles différents,
        // nous allons créer 36 cartes (2 pour chaque symbole)
        $this->cards = range(1, $pairs);
        $this->cards = array_merge($this->cards, $this->cards);

        // Mélangeons les cartes au début de chaque nouvelle partie
        $this->shuffleCards();
    }

    public function shuffleCards(): void
    {
        shuffle($this->cards);
    }

    /**
     * @return array
     */
    public function getCards(): array
    {
        return $this->cards;
    }
}