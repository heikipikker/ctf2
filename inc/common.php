<?php
    // Common Functions
    include('config.php');

    // Connect to mysql database each time this file is included

    $mysqli = new mysqli($config['mysql_host'], $config['mysql_user'], $config['mysql_pass'], $config['mysql_db']);
    if ($mysqli->connect_errno) {
        echo "<pre>Failed to connect to MySQL: " . $mysqli->connect_error ."\n\n";
        echo "Someone might not have played nice and messed with the box.\nEmail me at donnchacarroll@gmail.com or @DonnchaC and I'll fix it.</pre>";
        die();
    }
    
    function uploadError($params) {
        $response['status'] = 'fail';
        $response['info']   = '<p>'.$params['msg'].'</p>';
        echo json_encode($response);
        die();
    }

    function checkIfExists($sha256) {
        global $mysqli;
        
        $sha256 = $mysqli->real_escape_string($sha256); // Should already have been sanitized
        $result = $mysqli->query("SELECT id FROM uploads WHERE filehash = '". $sha256 ."'");
        
        if($result->num_rows > 0){  //Check if a file with this hash was already uploaded
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getUploadURL($sha256) {
        global $config;
        return $config['base_url'] . $config['upload_url'] . '?sha256=' . $sha256;
    }
    

    function diverse_array($vector) {
        $result = array();
        foreach($vector as $key1 => $value1)
            foreach($value1 as $key2 => $value2)
                $result[$key2][$key1] = $value2;
        return $result;
    }

    function checkMime($filename) {

        $mime_types = array(
        'txt' => 'text/plain',

        // images
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',

        // ms office
        'doc' => 'application/msword',
        'docx' => 'application/msword',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        
        $ext = strtolower(array_pop(explode('.',$filename)));
            
        if(function_exists('mime_content_type')) { 
            $mimetype = mime_content_type($filename); 
            return $mimetype; 

        } elseif(function_exists('finfo_open')) { 
            $finfo = finfo_open(FILEINFO_MIME); 
            $mimetype = finfo_file($finfo, $filename); 
            finfo_close($finfo); 
            return $mimetype; 
        } elseif(array_key_exists($ext, $mime_types)) { 
            return $mime_types[$ext]; 
        } else { 
            return FALSE; 
        } 
    }
?>
