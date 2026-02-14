<?php
header('Content-Type: application/json');

$labels = [];
$in_data = [];
$out_data = [];
$clicks_data = [];

for ($i = 0; $i < HOURS_PER_DAY; $i++) {
    $labels[] = sprintf('%02d:00', $i);
    $in_data[] = (int)$stats->i_raw[$i];
    $out_data[] = (int)$stats->o_raw[$i];
    $clicks_data[] = (int)$stats->c_raw[$i];
}

$chart_data = [
    'title' => 'Last 24 Hours In, Out, & Clicks',
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