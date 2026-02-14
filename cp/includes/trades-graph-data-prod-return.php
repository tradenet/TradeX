<?php
header('Content-Type: application/json');

$labels = [];
$prod_data = [];
$return_data = [];

for ($i = 0; $i < HOURS_PER_DAY; $i++) {
    $labels[] = sprintf('%02d:00', $i);
    $prod_data[] = (float)$stats->prod[$i];
    $return_data[] = (float)$stats->return[$i];
}

$chart_data = [
    'title' => 'Last 24 Hours Productivity & Return',
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'Productivity',
            'data' => $prod_data,
            'backgroundColor' => 'rgba(255, 0, 0, 0.1)',
            'borderColor' => 'rgba(255, 0, 0, 1)',
            'borderWidth' => 3,
            'fill' => false,
            'tension' => 0.1,
            'pointRadius' => 4,
            'pointHoverRadius' => 6
        ],
        [
            'label' => 'Return',
            'data' => $return_data,
            'backgroundColor' => 'rgba(0, 151, 255, 0.1)',
            'borderColor' => 'rgba(0, 151, 255, 1)',
            'borderWidth' => 3,
            'fill' => false,
            'tension' => 0.1,
            'pointRadius' => 4,
            'pointHoverRadius' => 6
        ]
    ]
];

echo json_encode($chart_data);