<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Function to read JSON data from file
function readVotes($pollId) {
    $filename = "polls/{$pollId}.json";
    if (file_exists($filename)) {
        $json = file_get_contents($filename);
        return json_decode($json, true);
    }
    return ['votes' => []];
}

// Function to write JSON data to file
function writeVotes($pollId, $data) {
    $filename = "polls/{$pollId}.json";
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Handle the vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['pollId']) && isset($input['option'])) {
        $pollId = $input['pollId'];
        $option = $input['option'];
        
        // Read current votes
        $data = readVotes($pollId);
        
        // Initialize option if it doesn't exist
        if (!isset($data['votes'][$option])) {
            $data['votes'][$option] = 0;
        }
        
        // Increment vote count
        $data['votes'][$option]++;
        
        // Save updated votes
        writeVotes($pollId, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Vote recorded successfully',
            'votes' => $data['votes']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request parameters'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>