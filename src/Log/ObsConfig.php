<?php


namespace Loouss\ObsClient\Log;

class ObsConfig
{
    const LOG_FILE_CONFIG = [
        'FilePath' => './logs',
        'FileName' => 'eSDK-OBS-PHP.log',
        'MaxFiles' => 10,
        'Level' => INFO
    ];
}
