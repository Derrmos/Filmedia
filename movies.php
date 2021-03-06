<?php
    $page_title = "Filmedia - Tous les films" ;
    $page_description = "Accédez à toutes les catégories de films";
    require './include/header.inc.php';
?>
    <main>
        <h1>Films</h1>
        <section id="movies-tendances">
            <h2>Tendances du moment</h2>
            <div class="horizontal-scroll">
<?php
    $trends = getWeekMoviesTrends();
    foreach ($trends as $trend) {
        echo "<a href=\"./details.php?id=" . $trend->id . "&amp;type=movie\">\n";
        echo "\t<article>\n";
        if (isset($trend->poster_path)) {
            echo "\t\t<img src=\"https://image.tmdb.org/t/p/w185" . $trend->poster_path . "\" alt=\"Affiche de " . htmlspecialchars($trend->title) . "\"/>\n";
        } else {
            echo "\t\t<img src=\"./img/no-image.svg\" width=\"185\" alt=\"no image\"/>\n";
        }
        echo "\t\t<div class=\"info\">\n";
        echo "\t\t\t<h3>" . htmlspecialchars($trend->title) . "</h3>\n";
        echo "\t\t\t<p>" . strftime("%d %b %Y", date_timestamp_get(date_create($trend->release_date))) . "</p>\n";
        echo "\t\t</div>\n";
        echo "\t</article>\n";
        echo "</a>\n";
    }
?>
            </div>
        </section>        
<?php
    $genres = getMovieGenres();
    foreach ($genres as $genre) {
        echo "<section style=\"margin-top:4rem; margin-bottom: 4rem;\">\n";
        echo "\t<h2>" . htmlspecialchars($genre->name) . "</h2>\n";
        echo "\t<div class=\"horizontal-scroll\">\n";

        $movies = getPopularMovieByGenre($genre->id);

        foreach($movies as $movie){
            echo "<a href=\"./details.php?id=" . $movie->id . "&amp;type=movie\">\n";
            echo "\t<article>\n";
            if (isset($movie->poster_path)) {
                echo "\t\t<img src=\"https://image.tmdb.org/t/p/w185" . $movie->poster_path . "\" alt=\"Affiche de " . htmlspecialchars($movie->title) . "\"/>\n";
            } else {
                echo "\t\t<img src=\"./img/no-image.svg\" width=\"185\" alt=\"no image\"/>\n";
            }
            echo "\t\t<div class=\"info\">\n";
            echo "\t\t\t<h3>" . htmlspecialchars($movie->title) . "</h3>\n";
            echo "\t\t\t<p>" . strftime("%d %b %Y", date_timestamp_get(date_create($movie->release_date))) . "</p>\n";
            echo "\t\t</div>\n";
            echo "\t</article>\n";
            echo "</a>\n";
        }

        echo "\t</div>\n";
        echo "</section>\n";
    }
?>
    </main>
<?php
    require './include/footer.inc.php';
?>