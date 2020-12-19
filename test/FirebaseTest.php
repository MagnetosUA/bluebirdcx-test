<?php

namespace App\Test;

use App\Firebase;
use App\Config\Config;
use GuzzleHttp\Client;

require_once __DIR__ . "/../vendor/autoload.php";

class FirebaseTest
{
    /**
     * Make request by GuzzleHttp
     * @param array $testData - testing data for updating Firestore collection
     */
    private function makeRequest(array $testData) {
        $client = new Client();
        $res = $client->request('PUT', 'http://127.0.0.1:8080/edit', ['json' => $testData]);
        return $res;
    }


    public function editSuccessTest()
    {
        $testData = [
            'doc_id' => 'second',
            'org_id' => '123ab',
            'email' => 'abc@company.com',
            'countries' => [
                'Ukraine',
                'USA'
            ]
        ];

        $res = $this->makeRequest($testData);

        if ($res->getStatusCode() == 200) {
            echo "First test is successful!" . PHP_EOL;
        } else {
            echo "First test ERROR" . PHP_EOL;
        }
    }

    public function editMissingRequired()
    {
        $testData = [
            'doc_id' => 'second',
            'org_id' => '',
            'email' => 'abc@company.com'
        ];

        try {
            $this->makeRequest($testData);
            echo "Second test ERROR" . PHP_EOL;
        } catch (\Exception $exception) {
            echo "Second test is successful!" . PHP_EOL;
        }
    }

    public function editInvalidSubItemRef()
    {
        $testData = [
            'doc_id' => 'second',
            'org_id' => '123jio',
            'email' => 'abc@company.com',
            'languages' => [
                'doc'
            ]
        ];

        try {
            $this->makeRequest($testData);
            echo "Third test ERROR" . PHP_EOL;
        } catch (\Exception $exception) {
            echo "Third test is successful!" . PHP_EOL;
        }
    }
}

$firebase = new FirebaseTest();
$firebase->editSuccessTest();
$firebase->editMissingRequired();
$firebase->editInvalidSubItemRef();
