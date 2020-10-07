
<?php 
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
//get image
if(isset($_POST['img'])){
    $url = $_POST['img'];
    
    $filename = 'D:\Coding\Website\www\PythonCall\file\\' . GUID() . '.png';
    try {
        file_put_contents($filename, file_get_contents($url));
        //find reg nr
        $output = json_decode(shell_exec('"C:\Program Files (x86)\openalpr_64\alpr.exe" '.$filename.' -c eu --json"'));
        if($output->results == null){
            echo json_encode(array("result" => "none"));
        }else{
            $re = '/[A-Z]{3}\d{2}([A-Z]|\d)/';//year regex
            $found = false;
            foreach ($output->results[0]->candidates as $candidate) {
                if(!$found){
                    if(preg_match($re,$candidate->plate)){
                        echo json_encode(array("result" => $candidate->plate));
                        $found = true;
                    }
                }
                
            }
            if($found == false){
                echo json_encode(array("result" => "none"));
            }
            
        }
    } catch (Exception $e) {
        //to do logging
    }
    

}
    
    
?>


