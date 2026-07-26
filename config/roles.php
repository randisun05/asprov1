<?php

/*
|--------------------------------------------------------------------------
| Admin panel roles
|--------------------------------------------------------------------------
|
| Single source of truth for the roles a staff (User) account can hold.
| Used to validate the role field on create/update and to populate the
| role dropdown on the frontend, so the backend whitelist and the UI
| options never drift apart again.
|
*/

return [
    'administrator' => 'Administrator',
    'humas' => 'Humas',
    'keanggotaan' => 'Keanggotaan',
    'pendanaan' => 'Pendanaan',
    'hukum' => 'Hukum',
    'kapasitas' => 'Kapasitas Insani',
    'sekretariat' => 'Sekretariat',
];
