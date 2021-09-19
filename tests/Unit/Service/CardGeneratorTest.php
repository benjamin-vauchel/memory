<?php

namespace Unit\Service;

use App\Service\CardGenerator;
use PHPUnit\Framework\TestCase;

class CardGeneratorTest extends TestCase
{
    private CardGenerator $cardGenerator;

    public function setUp(): void
    {
        $this->cardGenerator = new CardGenerator();
    }

    public function testGenerateCardsWhenPairsNumberIsValid(): void
    {
        $validPairNumber       = 5;
        $expectedNumberOfCards = 10;
        $expectedSortedCards   = [1, 1, 2, 2, 3, 3, 4, 4, 5, 5];

        $cards = $this->cardGenerator->generateCards($validPairNumber);

        // On vérifie que l'on obtient bien 2 cartes par paires
        $this->assertCount($expectedNumberOfCards, $cards);

        // On vérfie que les cartes ont bien été mélangées
        $this->assertNotEquals($expectedSortedCards, $cards);

        // Puis on trie les cartes générées de la plus petite à la plus grande
        // pour pouvoir vérifier que les bons numéros de cartes ont été générés
        sort($cards);
        $this->assertEquals($expectedSortedCards, $cards);

    }

    public function testGenerateCardsWhenPairsNumberIsGreaterThanMax(): void
    {
        // On défini le message d'erreur que l'on s'attend à obtenir
        $this->expectExceptionMessage('Le nombre de paires doit être compris entre 1 et 18');

        $this->cardGenerator->generateCards(19);
    }

    public function testGenerateCardsWhenPairsNumberIsLowerThanZero(): void
    {
        // On défini le message d'erreur que l'on s'attend à obtenir
        $this->expectExceptionMessage('Le nombre de paires doit être compris entre 1 et 18');

        $this->cardGenerator->generateCards(-1);
    }
}
