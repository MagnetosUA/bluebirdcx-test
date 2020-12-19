<?php

namespace App;

use Google\Cloud\Firestore\FirestoreClient;

abstract class FirebaseConnect
{
    /**
     * @param $projectId - Firebase project Id
     * @param $keyFile - Firebase access key file
     * @return FirestoreClient
     * @throws \Google\Cloud\Core\Exception\GoogleException
     *
     */
    public static function getDB($projectId, $keyFile)
    {
        $db = new FirestoreClient([
            'projectId' => $projectId,
            'keyFile' => json_decode(file_get_contents($keyFile), true)
        ]);

        return $db;
    }
}

