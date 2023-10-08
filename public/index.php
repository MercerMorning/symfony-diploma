<?php

use App\Kernel;

//$path = '../src/Python/image.py';
//$result = shell_exec('python3 ' . $path);
//$ffmpeg = FFMpeg\FFMpeg::create();
//$video = $ffmpeg->open('video.mp4');
//var_dump(json_decode($result));
//exit();

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
