<?php

require_once "api_key.php";

// Returns location (zip) and radius (miles) from user
function getLocAndRad() {
    $input = [];

    if (isset($_GET["location"]) && isset($_GET["radius"]) && isset($_GET["startdate"]) && isset($_GET["enddate"])) {
        $input[0] = $_GET["location"];
        $input[1] = $_GET["radius"];
        $input[2] = $_GET["startdate"];
        $input[3] = $_GET["enddate"];
        return $input;
    }

    $input[0] = 97214;
    $input[1] = 5;
    $input[2] = date("Y-m-d");
    $input[3] = date("Y-m-d");
    return $input;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://api.jambase.com/events?zipCode=" . getLocAndRad()[0] . "&radius=" . getLocAndRad()[1] . "&startDate=" . getLocAndRad()[2] . "&endDate=" . getLocAndRad()[3] . "&page=0&api_key=" . $api_key);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$results = json_decode($response);
curl_close($ch);

?>

<!DOCTYPE html>
<html>
    <head>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,800" rel="stylesheet">
        <link href="styles.min.css" rel="stylesheet">
        <meta charset="utf-8">
        <title>Concert Finder</title>
    </head>
    <body>
        <div id="bg_image">
            <h1>Welcome</h1>
        </div>
        <div class="container">
            <div id="welcome" class="center">
                <h3>Enter a zip code and radius, and we will show you upcoming concerts!</h3><br />
            </div>
            <div id="form" class="center">
                <form method="get" action="index.php">
                    <div class="form-group">
                        <label for="location">location:</label>
                        <input type="number" name="location" id="location" class="form-group" placeholder="zip code" required />
                    </div>
                    <div class="form-group">
                        <label for="radius">radius:</label>
                        <input type="number" name="radius" id="radius" class="form-group" placeholder="in miles" required />
                    </div>
                    <div class="form-group">
                        <label for="startdate">start date:</label>
                        <input type="date" name="startdate" id="startdate" required />
                    </div>
                    <div class="form-group">
                        <label for="enddate">end date:</label>
                        <input type="date" name="enddate" id="enddate" required /><br />
                    </div>
                    <button type="submit" class="btn btn-sm">Submit</button>
                </form><br />
            </div>
            <?php if ($results) : ?>
                <table class="table table-striped table-hover">
                    <tr>
                        <th>Artist(s)</th>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Show Time</th>
                        <th>Ticket Link</th>
                    </tr>
                        <?php foreach($results->Events as $event) : ?>
                            <tr>
                                <td>
                                    <ul>
                                        <?php foreach($event->Artists as $artist) : ?>
                                            <li><?php echo $artist->Name ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </td>
                                <td>
                                    <?php echo (empty($event->Venue->Url)) ? $event->Venue->Name : "<a href=" . $event->Venue->Url . ">" . $event->Venue->Name . "</a>"; ?>
                                </td>
                                <td>
                                    <?php
                                        $date = new DateTime($event->Date);
                                        echo $date->format("m/d/Y");
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $time = new DateTime($event->Date);
                                        $time = $time->format("H:i");
                                        echo ($time === "00:00") ? "Check venue for time" : $time;
                                    ?>
                                </td>
                                <td>
                                    <?php echo (empty($event->TicketUrl)) ? "No ticket link available." : "<a href=" . $event->TicketUrl . ">Ticket Link</a>"; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                </table>
            <?php else : ?>
                <div id="no_results" class="center">
                    <h3>If you're seeing this message, there was either an error or no results were found.</h3>
                </div>
            <?php endif ?>
        </div>
    </body>
</html>
