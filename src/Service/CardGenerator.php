<?php

namespace App\Service;

use InvalidArgumentException;

class CardGenerator
{
    private const MAX_PAIRS = 18;

    public function generateCards(int $pairs): array
    {
        // On dispose de 18 symboles différents, on vérifie donc que le nombre de paires
        // à générer n'est pas supérieur à ce nombre ou simplement inférieur à 0
        if ($pairs > self::MAX_PAIRS || $pairs < 1) {
            throw new InvalidArgumentException('Le nombre de paires doit être compris entre 1 et ' . self::MAX_PAIRS);
        }

        // Les cartes sont simplement identifiées par un nombre
        // On génère donc une suite de nombre entre 1 et le nombre de paire demandé, par exemple 18
        // Cela va générer 18 cartes
        $cards = range(1, $pairs);
        $cards = array_merge($cards, $cards);

        // On mélange les cartes
        shuffle($cards);

        return $cards;
    }
}