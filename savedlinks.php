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
require_once 'lib/dirdb.php';

// Get link IDs from URL parameter (comma-separated)
$link_ids = array();
$error_message = null;

if (isset($_GET['links']) && !empty($_GET['links'])) {
    $link_ids = explode(',', $_GET['links']);
} else {
    $error_message = 'No saved links specified';
}

// If we have valid link IDs, load and display them
if (!empty($link_ids) && $error_message === null) {
    $db = new SavedLinksDB();
    $links = array();
    
    foreach ($link_ids as $link_id) {
        $link_id = trim($link_id);
        if (empty($link_id)) continue;
        
        $link_data = $db->Retrieve($link_id);
        if ($link_data !== null) {
            // Build the out.php URL for this link
            $params = array();
            
            // Always add link identifier
            $params[] = 'l=' . urlencode($link_id);
            
            switch ($link_data['type']) {
                case 'trade':
                    if (!empty($link_data['trade'])) {
                        $params[] = 't=' . urlencode($link_data['trade']);
                    }
                    break;
                    
                case 'scheme':
                    if (!empty($link_data['skim_scheme'])) {
                        $params[] = 'ss=' . urlencode($link_data['skim_scheme']);
                    }
                    if (!empty($link_data['content_url'])) {
                        $params[] = 'u=' . ($link_data['encoding'] == 'urlencode' ? urlencode($link_data['content_url']) : base64_encode($link_data['content_url']));
                    }
                    if (!empty($link_data['category'])) {
                        $params[] = 'c=' . urlencode($link_data['category']);
                    }
                    if (!empty($link_data['group'])) {
                        $params[] = 'g=' . urlencode($link_data['group']);
                    }
                    break;
                    
                case 'percent':
                    if (!empty($link_data['percent'])) {
                        $params[] = 's=' . $link_data['percent'];
                    }
                    if ($link_data['flag_fc'] == '1') {
                        $params[] = 'fc=1';
                    }
                    if (!empty($link_data['content_url'])) {
                        $params[] = 'u=' . ($link_data['encoding'] == 'urlencode' ? urlencode($link_data['content_url']) : base64_encode($link_data['content_url']));
                    }
                    if (!empty($link_data['category'])) {
                        $params[] = 'c=' . urlencode($link_data['category']);
                    }
                    if (!empty($link_data['group'])) {
                        $params[] = 'g=' . urlencode($link_data['group']);
                    }
                    break;
            }
            
            $link_data['out_url'] = $C['base_url'] . '/out.php?' . join('&', $params);
            $links[] = $link_data;
        }
    }
    
    if (empty($links)) {
        $error_message = 'No valid saved links found';
    } else {
        // Expand links with custom thumbnails into individual entries
        $expanded_links = array();
        foreach ($links as $link) {
            if (!empty($link['custom_thumbs'])) {
                $thumbs = explode("\n", trim($link['custom_thumbs']));
                foreach ($thumbs as $thumb) {
                    $thumb = trim($thumb);
                    if (!empty($thumb)) {
                        $link_copy = $link;
                        $link_copy['thumb_url'] = $thumb;
                        $expanded_links[] = $link_copy;
                        
                        // Stop at 36 thumbnails
                        if (count($expanded_links) >= 36) {
                            break 2;
                        }
                    }
                }
            } else {
                // Link with no thumbnails - use placeholder
                $link['thumb_url'] = '';
                $expanded_links[] = $link;
                
                // Stop at 36 entries
                if (count($expanded_links) >= 36) {
                    break;
                }
            }
        }
        
        // Debug: Log what we have
        if (empty($expanded_links)) {
            error_log("SavedLinks DEBUG: expanded_links is empty! Original links count: " . count($links));
            foreach ($links as $link) {
                error_log("SavedLinks DEBUG: Link ID: {$link['link_id']}, custom_thumbs length: " . strlen($link['custom_thumbs']));
            }
        }
        
        // Prepare the template data
        $t = new Template();
        $t->AssignByRef('g_config', $C);
        $t->Assign('g_links', $expanded_links);
        
        // Compile and display
        global $compiler;
        if (!isset($compiler)) {
            $compiler = new Compiler();
        }
        
        $compiled = $compiler->CompileFile('toplist-saved-links-36.tpl');
        
        if ($compiled === false) {
            $error_message = 'Template compilation error';
        } else {
            echo $t->Parse($compiled);
        }
    }
}

// Display error if needed
if ($error_message !== null) {
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Saved Links Error</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
        .error { color: #d00; font-size: 18px; }
    </style>
</head>
<body>
    <div class="error">
        <h2>Saved Links Error</h2>
        <p>' . htmlspecialchars($error_message) . '</p>
    </div>
</body>
</html>';
}
