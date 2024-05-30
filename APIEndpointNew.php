<?php
header('Content-Type: application/json');
$username = htmlspecialchars($_GET['username']);

$page_no = 1;
$continue = true;
$reposlist = [];

$fork = $_GET['fork'] ?? '';
$stars = $_GET['stars'] ?? '';
$size = $_GET['size'] ?? '';

do {
    // max 100 per page
    $url = "https://api.github.com/users/$username/repos?per_page=100&page=$page_no";
    $options = [
        'http' => [
            'method' => "GET",
            'header' => "User-Agent: bpanguluru"
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        echo json_encode(['error' => true, 'message' => 'No such user']);
        exit;
    }

    // Check for rate limit exceeded
    foreach ($http_response_header as $header) {
        if (strpos($header, 'X-RateLimit-Remaining: 0') !== false) {
            echo json_encode(['error' => true, 'message' => 'GitHub API rate limit exceeded']);
            exit;
        }
    }

    // turn te json of repos into arr
    $data = json_decode($response, true);
    
    //if there is no data on this page then done
    if (count($data) == 0) {
        $continue = false;
    } else {
        foreach ($data as $repo) {
            if (($fork === 'true' && $repo['fork'] === false) || ($fork === 'false' && $repo['fork'] === true)) continue;
            if (!empty($stars) && $repo['stargazers_count'] < $stars) continue;
            if (!empty($size) && $repo['size'] < $size) continue;

            $reposlist[] = $repo; // Add repo to list if it passes filters
        }
        $page_no++;
    }

} while ($continue);

$totalRepos = count($reposlist);
$totalForks = array_sum(array_column($reposlist, 'forks_count'));
$languageCounts = [];

//have to fix/redo this
foreach ($reposlist as $repo) {
    $lang = $repo['language'] ;
    if (!isset($languageCounts[$lang])) {
        $languageCounts[$lang] = 0;
    }
    $languageCounts[$lang]++;
}

arsort($languageCounts);

$data = [
    'total_repos' => $totalRepos,
    'total_forks' => $totalForks,
    'languages' => $languageCounts
];

echo json_encode($data);
?>