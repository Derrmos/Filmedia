<?php

require './include/header.inc.php';

if(isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['type']) && !empty($_GET['type'])) {
    $id = htmlspecialchars($_GET['id']);
    $type = htmlspecialchars($_GET['type']);
} else{
    header('Location: ./');
    exit();
}

switch ($type) {
    case 'movie':
        $media = getMovieDetails($id);
        break;
    case 'tv':
        $media = getTVDetails($id);
        break;
    case 'person':
        $media = getPersonDetails($id);
        break;
    default:
        header('Location: ./');
        exit();
}

if ($type == "movie") {
    $title = $media->title;
    $date = $media->release_date;
} else {
    $title = $media->name;
    $date = $media->first_air_date;
}
$displayDate = strftime("%d %b %Y", date_timestamp_get(date_create($date)));

if ($type == "movie") {
    foreach ($media->release_dates->results as $release_dates) {
        if ($release_dates->iso_3166_1 == "FR") {
            $date = $release_dates->release_dates[0]->release_date;
            $displayDate = strftime("%d %b %Y", date_timestamp_get(date_create($date))) . " (FR)";
        }
    }
}

if (date_timestamp_get(date_create($date)) > time()) {
    $displayDate = "Prochainement : le " . $displayDate;
}

$genres = "" ;
$i = 0 ;
foreach($media->genres as $genre){
    if($i != 0) $genres .= ", ";
    $genres .= $genre->name;
    $i++;
}

if(isset($media->poster_path)){
    $src = "https://image.tmdb.org/t/p/w300". $media->poster_path;
}else{
    $src = "./img/no-image.svg" ;
}
?>
    <main>
        <div id="back-stats">
            <a href="./"><i class="fas fa-long-arrow-alt-left"></i> Retour à l'accueil</a>
            <p>301 vues</p>
        </div>
        <h1>Vue d'ensemble</h1>
        <section id="details">
            <img src="<?php echo $src; ?>" width="300" alt="Affiche de <?php echo $title; ?>"/>
            <div id="details-info">
                <div id="details-info-title">
                    <h2><?php echo $title; ?></h2>

                    
                    <p><?php echo $displayDate; ?></p>
                    <p>Genres : <?php echo $genres; ?>
                </div>
                <div id="details-info-description">
                    <p><?php echo $media->overview; ?></p>
                </div>
<?php
    if(isset($media->{"watch/providers"}->results->FR)) {
        $providers = $media->{"watch/providers"}->results->FR;

        echo "<article id=\"details-provider\">\n";
        echo "\t<h3>Disponible sur :</h3>\n";

        if(isset($providers->buy)){
            echo "\t\t<p>A l'achat</p>\n";
            echo "\t\t<div class=\"provider-icon\">\n";
            foreach($providers->buy as $provider) {
                echo "\t\t\t<img src=\"https://image.tmdb.org/t/p/w45". $provider->logo_path ."\" alt=\"logo de ". $provider->provider_name ."\"/>\n";
            }
            echo "\t\t</div>\n";
        }
        if(isset($providers->rent)){
            echo "\t\t<p>A la location</p>\n";
            echo "\t\t<div class=\"provider-icon\">\n";
            foreach($providers->rent as $provider){
                echo "\t\t\t<img src=\"https://image.tmdb.org/t/p/w45". $provider->logo_path ."\" alt=\"logo de ". $provider->provider_name ."\"/>\n";
            }
            echo "\t\t</div>\n";
        }
        if(isset($providers->flatrate)){
            echo "\t\t<p>En streaming</p>\n";
            echo "\t\t<div class=\"provider-icon\">\n";
            foreach($providers->flatrate as $provider){
                echo "\t\t\t<img src=\"https://image.tmdb.org/t/p/w45". $provider->logo_path ."\" alt=\"logo de ". $provider->provider_name ."\"/>\n";
            }
            echo "\t\t</div>\n";
        }
        if(isset($providers->ads)){
            echo "\t\t<p>En streaming</p>\n";
            echo "\t\t<div class=\"provider-icon\">\n";
            foreach($providers->ads as $provider){
                echo "\t\t\t<img src=\"https://image.tmdb.org/t/p/w45". $provider->logo_path ."\" alt=\"logo de ". $provider->provider_name ."\"/>\n";
            }
            echo "\t\t</div>\n";
        }

        echo "</article>\n";
    }
?>
            </div>
            <aside>
<?php
    if (isset($media->credits->cast)) {
        $casts = $media->credits->cast;

        echo "<h3>Tête d'affiche :</h3>\n";
        echo "<div id=\"cast\">\n";

        $i = 0;
        while ($i < 4 && $i < count($casts)) {
            $cast = $casts[$i];
            echo "\t<article>\n";
            echo "\t\t<img src=\"https://image.tmdb.org/t/p/w92". $cast->profile_path ."\" alt=\"Photo de ". $cast->name ."\"/>\n";
            echo "\t\t<h4>". $cast->name ."</h4>\n";
            echo "\t\t<p>". $cast->character ."</p>\n";
            echo "\t</article>\n";
            $i++;
        }

        echo "</div>\n";
        
    }
?>
            </aside>
        </section>
<?php
    $i = 0;
    $found = false;
    while($i < count($media->videos->results) && $found == false) {
        $video = $media->videos->results[$i];
        if ($video->type == "Trailer") {
            echo "<article id=\"details-video\">\n";
            echo "\t<h2>Bande annonce</h2>\n";
            echo "\t<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/".$video->key."?controls=0\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>\n";
            echo "</article>\n";
            $found = true;
        }
        $i++;
    } 
    
?>
        <section id="tendances">
            <h2>Associés à <?php echo $title; ?></h2>
            <div class="horizontal-scroll">
<?php
$similars = $media->similar->results;
foreach($similars as $similar){
    if ($type == "movie") {
        $title = $similar->title;
        $date = $similar->release_date;
    } else {
        $title = $similar->name;
        $date = $similar->first_air_date;
    }
    echo "<a href=\"./details.php?id=" . $similar->id . "&amp;type=". $type ."\">\n";
    echo "<article id=\"similar-". $similar->id ."\">\n";
    echo "\t<img src=\"https://image.tmdb.org/t/p/w185". $similar->poster_path ."\" alt=\"Affiche de ". $title ."\"/>\n";
    echo "\t<h3>". $title ."</h3>\n";
    echo "\t\t<p>" . strftime("%d %b %Y", date_timestamp_get(date_create($date))) . "</p>\n";
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