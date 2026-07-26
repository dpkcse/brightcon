<?php

return [
    'enforce' => env('CMS_INSTALLER_ENFORCE', true),
    'execution_attempts' => (int) env('CMS_INSTALLER_EXECUTION_ATTEMPTS', 5),
    'execution_decay_minutes' => (int) env('CMS_INSTALLER_EXECUTION_DECAY_MINUTES', 10),
];
