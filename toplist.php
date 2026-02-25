<?php
// Copyright 2011 JMB Software, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


define('TRADEX', true);

require_once 'lib/global.php';
require_once 'lib/compiler.php';
require_once 'lib/template.php';
require_once 'lib/utility.php';

// Get the toplist data from the URL parameter
$toplist_data = null;
$error_message = null;

if (isset($_GET['d']) && !empty($_GET['d'])) {
    // Decode the toplist data (base64 encoded JSON)
    $decoded = base64_decode($_GET['d']);
    if ($decoded !== false) {
        $toplist_data = json_decode($decoded, true);
        
        if (!is_array($toplist_data) || empty($toplist_data['trades'])) {
            $error_message = 'Invalid toplist data';
            $toplist_data = null;
        }
    } else {
        $error_message = 'Unable to decode toplist data';
    }
} else {
    $error_message = 'No toplist data provided';
}

// If we have valid toplist data, render it
if ($toplist_data !== null && is_array($toplist_data['trades'])) {
    // Prepare the template data
    $t = new Template();
    $t->AssignByRef('g_config', $C);
    
    // Build the trades array for the template
    $trades = array();
    foreach ($toplist_data['trades'] as $trade_data) {
        $trade = array(
            'domain' => $trade_data['domain'],
            'site_name' => isset($trade_data['site_name']) ? $trade_data['site_name'] : '',
            'custom_thumbs' => isset($trade_data['thumbs']) ? $trade_data['thumbs'] : ''
        );
        $trades[] = $trade;
    }
    
    $t->Assign('g_trades', $trades);
    
    // Use the custom thumbs template
    $template = isset($toplist_data['template']) ? $toplist_data['template'] : 'toplist-random-36-custom-thumbs.tpl';
    
    // Compile and display
    global $compiler;
    $compiled = $compiler->Compile($template);
    echo $t->Parse($compiled);
} else {
    // Display error message
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Toplist Error</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
        .error { color: #d00; font-size: 18px; }
    </style>
</head>
<body>
    <div class="error">
        <h2>Toplist Error</h2>
        <p>' . htmlspecialchars($error_message) . '</p>
    </div>
</body>
</html>';
}
