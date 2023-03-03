<?php

namespace App\Service;

use Kreait\Firebase\Factory;

class KreaitService
{
    public static function messaging()
    {
        $credentialPath = public_path(env('FIREBASE_CREDENTIAL'));
        $factory = (new Factory)->withServiceAccount($credentialPath);

        return $factory->createMessaging();
    }
}