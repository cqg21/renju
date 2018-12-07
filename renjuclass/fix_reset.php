<?php
ini_set('memory_limit','2048M');

/**
 * @param $file
 * @return array
 */
function fix_reset($file)
{
    $content = json_decode(file_get_contents($file),1);
    $data = [];

    foreach ($content as $row)
    {
        $data[] = $row;
        if($row['action'] == 'RESET' && $row['content'] != '000000')
        {
            $load = substr($row['content'],6);
            $tmp = '';
            for($i = 0 ; $i < strlen($load) ; $i ++)
            {
                $tmp .= dechex(ord($load{$i}) - 64);
            }
            $load = $tmp;
            $data[] = [
                'action' => 'LOAD',
                'time' => $row['time'],
                'time_int' => $row['time_int'],
                'user' => $row['user'],
                'content' => $load,
            ];
            $data[] = [
                'action' => 'GOTO',
                'time' => $row['time'],
                'time_int' => $row['time_int'],
                'user' => $row['user'],
                'content' => intval(substr($row['content'],3,3)),
            ];
        }
    }
    return $data;
}

$dir = __DIR__.'/json/';
$fhandler = opendir($dir);
while ($fname = readdir($fhandler))
{
    if(preg_match('/^(\d+)\.json$/i',$fname,$match))
    {
        $file = $dir.$fname;
        $content = fix_reset($file);
        file_put_contents($file,json_encode($content,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

