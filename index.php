<?php
/**
 * Site infini pour tests de crawl.
 * Toutes les routes mènent ici. Chaque page :
 *  - a un <title> et un <h1> uniques (seed dérivé de l'URL)
 *  - affiche du faux texte
 *  - génère 100 liens vers d'autres URLs aléatoires
 *
 * Lancement :
 *   php -S 0.0.0.0:8000 index.php
 * Puis crawle http://localhost:8000/
 */

// --- Seed déterministe basé sur le chemin de l'URL ---
// Comme ça la même URL redonne toujours la même page (utile pour un crawler).
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$seed = crc32($path);
mt_srand($seed);

// --- Petit dictionnaire pour fabriquer du texte et des slugs crédibles ---
$mots = [
    'alpha','beta','gamma','delta','epsilon','zeta','eta','theta','iota','kappa',
    'lambda','sigma','omega','nova','quartz','nebula','cosmos','vector','matrix','photon',
    'lumen','cobalt','azur','onyx','ambre','silex','granit','basalt','prisme','vortex',
    'horizon','meridien','zenith','aurore','crepuscule','marais','toundra','recif','dune','glacier',
    'fabrique','atelier','comptoir','manufacture','guilde','syndicat','fragment','relique','artefact','grimoire',
];

function mot_aleatoire($mots) {
    return $mots[mt_rand(0, count($mots) - 1)];
}

function slug_aleatoire($mots) {
    $n = mt_rand(2, 3);
    $parts = [];
    for ($i = 0; $i < $n; $i++) {
        $parts[] = mot_aleatoire($mots);
    }
    // suffixe court pour augmenter la cardinalité (évite les collisions de slugs)
    $parts[] = substr(md5(mt_rand()), 0, 6);
    return implode('-', $parts);
}

function phrase($mots, $minMots = 8, $maxMots = 18) {
    $n = mt_rand($minMots, $maxMots);
    $out = [];
    for ($i = 0; $i < $n; $i++) {
        $out[] = mot_aleatoire($mots);
    }
    $s = implode(' ', $out);
    return ucfirst($s) . '.';
}

// --- Construction de la page ---
$titre = ucwords(str_replace('-', ' ', slug_aleatoire($mots)));
$h1    = ucwords(str_replace('-', ' ', slug_aleatoire($mots)));

// Profondeur visible (juste informatif, à partir du nb de segments d'URL)
$profondeur = max(0, count(array_filter(explode('/', trim($path, '/')))));

// 100 liens enfants
$liens = [];
for ($i = 0; $i < 100; $i++) {
    $liens[] = '/' . slug_aleatoire($mots);
}

header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titre) ?></title>
    <meta name="description" content="<?= htmlspecialchars(phrase($mots, 10, 20)) ?>">
    <style>
        :root { --bg:#0f1115; --fg:#e6e6e6; --accent:#6ea8fe; --muted:#8a8f98; }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0 0 4rem;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6; color: var(--fg); background: var(--bg);
        }
        header {
            padding: 3rem 1.5rem 2rem; border-bottom: 1px solid #222;
            background: linear-gradient(180deg, #161922, #0f1115);
        }
        .wrap { max-width: 980px; margin: 0 auto; padding: 0 1.5rem; }
        h1 { font-size: 2.2rem; margin: 0 0 .5rem; }
        .meta { color: var(--muted); font-size: .9rem; }
        h2 { margin-top: 2.5rem; font-size: 1.3rem; }
        p { margin: 1rem 0; }
        nav ul { list-style: none; padding: 0; margin: 1rem 0;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap: .4rem .9rem; }
        nav a { color: var(--accent); text-decoration: none; font-size: .92rem; }
        nav a:hover { text-decoration: underline; }
        footer { margin-top: 3rem; color: var(--muted); font-size: .85rem; }
    </style>
</head>
<body>
    <header>
        <div class="wrap">
            <h1><?= htmlspecialchars($h1) ?></h1>
            <p class="meta">URL : <?= htmlspecialchars($path) ?> · profondeur d'URL : <?= $profondeur ?> · seed : <?= $seed ?></p>
        </div>
    </header>

    <main class="wrap">
        <article>
            <p><?= htmlspecialchars(phrase($mots, 15, 30)) ?></p>
            <p><?= htmlspecialchars(phrase($mots, 15, 30)) ?></p>
            <h2><?= htmlspecialchars(ucwords(str_replace('-', ' ', slug_aleatoire($mots)))) ?></h2>
            <p><?= htmlspecialchars(phrase($mots, 20, 40)) ?></p>
            <p><?= htmlspecialchars(phrase($mots, 20, 40)) ?></p>
        </article>

        <h2>Explorer (<?= count($liens) ?> liens)</h2>
        <nav>
            <ul>
                <?php foreach ($liens as $href): ?>
                    <li><a href="<?= htmlspecialchars($href) ?>"><?= htmlspecialchars(ucwords(str_replace(['/', '-'], ['', ' '], $href))) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <footer>
            Page générée dynamiquement · site de test pour crawler · <?= date('Y-m-d H:i:s') ?>
        </footer>
    </main>
</body>
</html>
