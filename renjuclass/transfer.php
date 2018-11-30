<?php
ini_set('memory_limit','2048M');

/**
 * @param $file
 * @return array
 */
function transfer($file)
{
    $content = file_get_contents($file);
    $content = mb_convert_encoding($content,'UTF-8','GB18030');
    $split = explode("\n",$content);
    $data = [];

    foreach ($split as $row)
    {
        $row = trim($row);
        if($row == '')
        {
            continue;
        }
        if(!preg_match('/^\<(\d{2}:\d{2}:\d{2})\>\[([a-z]+)\](?:\{([^}]+)\})?(.*)$/i',$row,$match))
        {
            //echo ("{$row} at {$file} does not match\n");
            //拼到上一行去！
            end($data);
            $last_key = key($data);
            if($last_key)
            {
                $data[$last_key]['content'] .= "\n" . $row;
            }
            continue;
        }
        //time
        $time_split = explode(':',$match[1]);
        $time_int = ($time_split[0] * 3600) + ($time_split[1] * 60) + $time_split[2];
        //time
        //action and content 坐标转换。
        $content = $match[4];

        if($match[2] == 'GOTO')
        {
            $content = intval($content);
        }
        if($match[2] == 'LOAD')
        {
            $content = substr($content,6);
        }
        if($match[2] == 'MOVE' || $match[2] == 'LOAD')
        {
            $tmp = '';
            for($i = 0 ; $i < strlen($content) ; $i ++)
            {
                $tmp .= dechex(ord($content{$i}) - 64);
            }
            $content = $tmp;
        }
        $data[] = [
            'action' => $match[2],
            'time' => $match[1],
            'time_int' => $time_int,
            'user' => $match[3],
            'content' => $content,
        ];
    }
    return $data;
}

$transferred = [];
$actions = [];
$dir = __DIR__.'/data/';
$fhandler = opendir($dir);
while ($fname = readdir($fhandler))
{
    if(preg_match('/^(\d+).*\.orc$/i',$fname,$match))
    {
        $file = $dir.$fname;
        $content = transfer($file);
        $output = $match[1].'.json';
        $transferred[] = [
            'show_name' => $fname,
            'data' => $output,
            'source_file_name' => $fname,
            //'data' => $content
        ];
        $actions = array_unique($actions + array_column($content,'action'));
        file_put_contents(__DIR__.'/json/'.$output,json_encode($content,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

var_dump($actions);
//echo json_encode($transferred,JSON_PRETTY_PRINT);
