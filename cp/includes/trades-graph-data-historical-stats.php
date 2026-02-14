<?php
header('Content-Type: application/json');

$labels = [];
$in_data = [];
$out_data = [];
$clicks_data = [];

if (is_array($history->stats)) {
    foreach ($history->stats as $date => $stats) {
        $labels[] = $date;
        $in_data[] = (int)$stats[0];
        $out_data[] = (int)$stats[14];
        $clicks_data[] = (int)$stats[6];
    }
}

$chart_data = [
    'title' => 'Historical In, Out, & Clicks',
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'In',
            'data' => $in_data,
            'backgroundColor' => 'rgba(246, 189, 17, 0.85)',
            'borderColor' => 'rgba(246, 189, 17, 1)',
            'borderWidth' => 1
        ],
        [
            'label' => 'Out',
            'data' => $out_data,
            'backgroundColor' => 'rgba(177, 217, 248, 0.85)',
            'borderColor' => 'rgba(177, 217, 248, 1)',
            'borderWidth' => 1
        ],
        [
            'label' => 'Clicks',
            'data' => $clicks_data,
            'backgroundColor' => 'rgba(141, 187, 5, 0.85)',
            'borderColor' => 'rgba(141, 187, 5, 1)',
            'borderWidth' => 1
        ]
    ]
];

echo json_encode($chart_data);
