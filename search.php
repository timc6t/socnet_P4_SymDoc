<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = $_POST["search_query"];
    $searchType = $_POST["search_type"];

    if ($searchType === "users") {
        $results = searchUsers($searchQuery);
    } elseif ($searchType === "texts") {
        $results = searchTexts($searchQuery);
    }
}

function searchUsers($Query) {

}

function searchTexts($Query) {
 
}