<?php
    $page_description = "Page d'accueil de Filmedia";
    require './include/header.inc.php';

    if (isset($_COOKIE['last_media_consulted']) && !empty($_COOKIE['last_media_consulted'])) {
        $lastMedia = json_decode($_COOKIE['last_media_consulted'], true);
    }
?>
    <main>
        <h1>Filmedia - Trouvez ce qui vous plait !</h1>
        <section id="welcome-search">
            <h2>Que recherchez-vous ?</h2>
            <form action="./search.php" method="get">
                <input type="text" name="q" id="search-bar" placeholder="Films, séries, ..." />
                <input type="submit" value="Rechercher" />
            </form>
<?php
    if (isset($lastMedia)) {
        echo "<article id=\"last-film\">\n";
        echo "\t<h3 style=\"text-align: center;\">Dernière visite</h3>\n";
        echo "\t<a href=\"./details.php?id=" . $lastMedia['id'] . "&amp;type=". $lastMedia['type'] ."\">\n";
        echo "\t\t<div id=\"last-film-box\">\n";
        echo "\t\t\t<img src=\"https://image.tmdb.org/t/p/w92". $lastMedia['poster'] ."\" alt=\"". $lastMedia['name'] . "\" />\n";
        echo "\t\t\t<div id=\"last-film-info\">\n";
        echo "\t\t\t<strong>" . $lastMedia['name'] ."</strong>\n";
        echo "\t\t\t<p>Consulté le " . $lastMedia['date'] . "</p>\n";
        echo "\t\t\t</div>\n";
        echo "\t\t</div>\n";
        echo "\t</a>\n";
        echo "</article>\n";
    }
?>
        </section>
        <section id="actuals-media">
            <h2>Films à l'affiche</h2>
            <div class="horizontal-scroll">
<?php
$movies = getNowPlayingMovies();
foreach($movies as $movie){
    echo "<a href=\"./details.php?id=" . $movie->id . "&amp;type=movie\">\n";
    echo "\t<article>\n";
    if(isset($movie->poster_path)){
        echo "\t\t<img src=\"https://image.tmdb.org/t/p/w185" . $movie->poster_path . "\" alt=\"Affiche de " . htmlspecialchars($movie->title) . "\"/>\n";
    }else{
        echo "\t\t<img src=\"./img/no-image.svg\" width=\"185\" alt=\"no image\"/>\n";
    }
    echo "\t\t<div class=\"info\">\n";
    echo "\t\t\t<h3>" . htmlspecialchars($movie->title) . "</h3>\n";
    echo "\t\t\t<p>" . strftime("%d %b %Y", date_timestamp_get(date_create($movie->release_date))) . "</p>\n";
    echo "\t\t</div>\n";
    echo "\t</article>\n";
    echo "</a>\n";
}
?>
            </div>
        </section>
        <section id="tendances">
            <h2>Tendances du moment</h2>
            <div class="horizontal-scroll">
<?php
$trends = getWeekTrends();
foreach($trends as $trend){
    if ($trend->media_type == "movie") {
        $title = htmlspecialchars($trend->title);
        $date = $trend->release_date;
    } else {
        $title = htmlspecialchars($trend->name);
        $date = $trend->first_air_date;
    }
    echo "<a href=\"./details.php?id=" . $trend->id . "&amp;type=". $trend->media_type ."\">\n";
    echo "<article>\n";
    if(isset($trend->poster_path)){
        echo "\t\t<img src=\"https://image.tmdb.org/t/p/w185". $trend->poster_path ."\" alt=\"Affiche de ". $title ."\"/>\n";
    }else{
        echo "\t\t<img src=\"./img/no-image.svg\" width=\"185\" alt=\"no image\"/>\n";
    }
    echo "\t\t<div class=\"info\">\n";
    echo "\t\t\t<h3>". $title ."</h3>\n";
    echo "\t\t\t<p>" . strftime("%d %b %Y", date_timestamp_get(date_create($date))) . "</p>\n";
    echo "\t\t</div>\n";
    echo "</article>\n";
    echo "</a>\n";
}
?>
            </div>
        </section>
    </main>
<?php
    require './include/footer.inc.php';
?>