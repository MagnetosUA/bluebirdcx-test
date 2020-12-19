<?php

namespace App;

use App\FirebaseConnect;
use Google\Cloud\Firestore\FieldValue;

class Firebase
{
    private $db;

    /**
     * Init Firestore connection
     */
    public function __construct($projectId, $keyFile)
    {
        $this->db = FirebaseConnect::getDB($projectId, $keyFile);
    }

    /**
     * @param $collection - Firestore collection name
     * @param $document - Firestore document name
     */
    public function get($collection, $document)
    {
        $data = $this->db->collection($collection)->document($document)->snapshot()->data();

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                foreach ($item as $val) {
                    $arr = explode('/', $val->name());
                    $name = end($arr);
                    $doc = $this->db->collection($collection)->document($name)->snapshot()->data();
                    print_r([$name => $doc]);
                }
            } else {
                print_r("{$key} => {$item}" . PHP_EOL);
            }
        }
    }

    /**
     * Check if json data is valid
     */
    private function isValidData() {
        $resv = json_last_error() == JSON_ERROR_NONE;
        return $resv;
    }

    /**
     * Check if object has any parameters
     * @param $obj - PHP object after json decoding
     */
    private function isEmptyData($obj)
    {
        return empty(get_object_vars($obj));
    }

    /**
     * Check if email address is valid
     * @param $str - email address
     */
    public function validEmail($str) {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;
    }

    /**
     * Set new values for Firestore collection
     * @param string $collection - Firestore collection name
     * @param string $document - Firestore document name
     */
    public function set(string $collection, string $document)
    {
        // Takes raw data from the request
        $json = file_get_contents('php://input');

        // Converts it into a PHP object
        $data = json_decode($json);

        if ($this->isValidData() && !$this->isEmptyData($data)) {
            $postParams = [];

            // Get document id from received data
            $docId = $data->doc_id ?? null ;

            if ($docId) {
                unset($data->doc_id);
            } else {
                header("Error! The doc_id field does not exists!", '', '400');
                echo "Error! The doc_id field does not exists!";
                return;
            }

            foreach ($data as $key => $val) {
                // Check if an array is exists in received data
                if (is_array($val)) {
                    $refsArr = [];
                    foreach ($val as $docName) {
                        // Check if a document reference is real
                        $refDoc = $this->db->collection('test_sushytsky')->document($docName)->snapshot();
                        if (!$refDoc->exists()) {
                            $type = gettype($val);
                            header("Error! Invalid sub_item ref. The document with name {$docName} does not exists.", '', '400');
                            echo "Error! Invalid sub_item ref. The document with name {$docName} does not exists.";
                            return;
                        }
                        $refsArr[] = $refDoc;
                    }

                    // Add new document references in array if they are received
                    if ($refsArr) {
                        $postParams[$key] = FieldValue::arrayUnion($refsArr);
                    } else {
                        $postParams[$key] = [];
                    }
                } else {
                    // Check if required field 'org_id' is not empty
                    if (($key == 'org_id') && $val == '') {
                        header("Error! The required field {$key} is empty", '', '400');
                        echo "Error! The required field {$key} is empty";
                        return;
                    }
                    // Check if required field 'email' is valid
                    if ($key == 'email' && !$this->validEmail($val)) {
                        header("Error! Wrong '{$key}' ! '{$val}' is not a valid {$key} address", '', '400');
                        echo "Error! Wrong '{$key}' ! '{$val}' is not a valid {$key} address";
                        return;
                    }
                    $postParams[$key] = $val;
                }
            }

            $updatedParameters = [];

            // Prepare data for updating
            foreach ($postParams as $key => $val) {
                $updatedParameters[] = ['path' => $key, 'value' => $val];
            }

            $this->db->collection($collection)->document($docId)->update($updatedParameters);
            header("The data is updated!", '', 200);
            print_r($updatedParameters);
            return;
        } else {
            header("Error! There is not valid or empty json!", '', '400');
            echo "Error! There is not valid or empty json!";
            return;
        }
    }
}

