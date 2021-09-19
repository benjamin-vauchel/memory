<?php

namespace Functionnal;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\Panther\PantherTestCase;

class GameTest extends PantherTestCase
{
    public function setUp(): void
    {
        // On vide la base de données avant chaque test
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $purger        = new ORMPurger($entityManager);
        $purger->purge();
    }

    public function testWhenPlayerWinTheGame(): void
    {
        // On charge la page / dans notre serveur web de test
        $client  = static::createPantherClient();
        $browser = $client->request('GET', '/');

        // On vérifie le titre de la page
        self::assertPageTitleContains('Memory');

        // On vérifie que l'on est bien sur l'écran d'accueil et que le tableau des scores n'est pas affiché
        self::assertSelectorIsVisible('.splash-screen');
        self::assertSelectorIsNotVisible('.game-screen');
        self::assertSelectorNotExists('.score-board');

        // On lance une nouvelle partie
        $form = $browser->filter('.form')->form();
        $form->setValues([
            'name' => '-_-',
        ]);
        $client->executeScript("document.querySelector('#new-game').click()");
        $client->waitForVisibility('.game-screen');
        self::assertSelectorIsNotVisible('.splash-screen');

        // On vérifie que l'on a bien 4 cartes en début de partie (cf config/services_test.yaml)
        $this->assertEquals(4, $browser->filter('.card')->count());

        // On retourne une mauvaise paire => on s'assure que les cartes sont bien retournées face cachée
        $client->executeScript("document.querySelector('[data-card-number=\"1\"]').click()");
        $client->executeScript("document.querySelector('[data-card-number=\"2\"]').click()");
        $client->waitFor('[data-card-number="1"].card--flipped-wrong');
        $client->waitFor('[data-card-number="2"].card--flipped-wrong');
        $this->assertEquals(1, $browser->filter('[data-card-number="1"].card--flipped-wrong')->count());
        $this->assertEquals(1, $browser->filter('[data-card-number="2"].card--flipped-wrong')->count());

        // On retourne les bonnes paires => on s'assure que les cartes restent bien face visibles
        $client->executeScript("document.querySelectorAll('[data-card-number=\"1\"]').forEach(e => e.click())");
        $this->assertEquals(2, $browser->filter('[data-card-number="1"].card--flipped-right')->count());
        $client->executeScript("document.querySelectorAll('[data-card-number=\"2\"]').forEach(e => e.click())");
        $client->wait()->until(WebDriverExpectedCondition::alertIsPresent());
        self::assertStringContainsString('Bravo -_-, vous avez terminé le jeu en', $client->getWebDriver()->switchTo()->alert()->getText());
        $client->getWebDriver()->switchTo()->alert()->accept();

        // On recharge la page et on vérifie que le score du joueur apparaisse dans le tableau des scores
        $browser = $client->request('GET', '/');
        self::assertSelectorExists('.score-board');
        $scoreLines = $browser->filter('.score-board tbody tr');
        $this->assertEquals(1, $scoreLines->count());
        $this->assertEquals("#1", $scoreLines->first()->filter('td')->eq(0)->first()->getText());
        $this->assertEquals("-_-", $scoreLines->first()->filter('td')->eq(1)->first()->getText());
    }

    public function testWhenPlayerLooseTheGame(): void
    {
        // On charge la page / dans notre serveur web de test
        $client  = static::createPantherClient();
        $browser = $client->request('GET', '/');

        // On vérifie le titre de la page
        self::assertPageTitleContains('Memory');

        // On vérifie que l'on est bien sur l'écran d'accueil et que le tableau des scores n'est pas affiché
        self::assertSelectorIsVisible('.splash-screen');
        self::assertSelectorIsNotVisible('.game-screen');
        self::assertSelectorNotExists('.score-board');

        // On lance une nouvelle partie
        $form = $browser->filter('.form')->form();
        $form->setValues([
            'name' => '-_-',
        ]);
        $client->executeScript("document.querySelector('#new-game').click()");
        $client->waitForVisibility('.game-screen');
        self::assertSelectorIsNotVisible('.splash-screen');

        // On attend l'alerte JS avec le message indiuant que la partie est perdue
        $client->getWebDriver()->wait()->until(WebDriverExpectedCondition::alertIsPresent());
        self::assertEquals('Désolé -_-, vous avez perdu :(',$client->getWebDriver()->switchTo()->alert()->getText());

        // On ferme l'alerte JS
        $client->getWebDriver()->switchTo()->alert()->accept();

        // On vérifie que le score du joueur apparaisse dans le tableau des scores
        self::assertSelectorNotExists('.score-board');
    }
}