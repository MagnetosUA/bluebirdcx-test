'use strict';

// Chai is a commonly used library for creating unit test suites. It is easily extended with plugins.
const chai = require('chai');
const assert = chai.assert;

const test = require('firebase-functions-test')({
    projectId: 'bluebirdcx-interview'
    },
    '/home/alex-su/Practice/FireBase/functions/test/bluebirdcx-interview-firebase-adminsdk-9avmg-a8170af84c.json',
);

const testEmailAddress = 'gemesac696@febeks.com';
const myFunctions = require('../index.js');
const wrapped = test.wrap(myFunctions.alexSendEmailNotification);
const beforeSnap = test.firestore.makeDocumentSnapshot({'org_id': '12345', 'email': testEmailAddress}, 'test_sushytsky/first');
const afterSnap = test.firestore.makeDocumentSnapshot({'org_id': '1234567', 'email': testEmailAddress}, 'test_sushytsky/first');
const change = test.makeChange(beforeSnap, afterSnap);

let result;

// Get result accepted delivered email address from alexSendEmailNotification function
wrapped(change).then(async (res) => {
    result = await res.accepted[0];
    console.log(result);
});

// Weiting for Promise and check compare accepted email from testEmail
setTimeout(()=>{
    assert.equal(result, testEmailAddress);
}, 3000);




