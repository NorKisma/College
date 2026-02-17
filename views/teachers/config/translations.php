<?php
function translate($key) {
    global $conn, $currentLangId;
    
    $stmt = $conn->prepare("SELECT translation_value FROM translations WHERE translation_key = ? AND language_id = ?");
    $stmt->bind_param("si", $key, $currentLangId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['translation_value'];
    }
    
    // Fallback to English
    if ($currentLangId != 1) {
        $stmt = $conn->prepare("SELECT translation_value FROM translations WHERE translation_key = ? AND language_id = 1");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['translation_value'];
        }
    }
    
    return $key;
}