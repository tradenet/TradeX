<?php
header('Content-Type: application/json');

$labels = [];
$prod_data = [];
$return_data = [];

if (is_array($history->stats)) {
    foreach ($history->stats as $date => $stats) {
        $labels[] = $date;
        $prod_data[] = $stats[0] > 0 ? (float)format_float_to_percent($stats[6] / $stats[0]) : 0;
        $return_data[] = $stats[0] > 0 ? (float)format_float_to_percent($stats[14] / $stats[0]) : 0;
    }
}

$chart_data = [
    'title' => 'Historical Productivity & Return',
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