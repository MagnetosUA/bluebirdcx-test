'use strict';

const functions = require('firebase-functions');
const nodemailer = require('nodemailer');
const escapeHtml = require('escape-html');
const gmailEmail = functions.config().gmail.email;
const gmailPassword = functions.config().gmail.password;
const mailTransport = nodemailer.createTransport({
  service: 'gmail',
  auth: {
    user: gmailEmail,
    pass: gmailPassword,
  },
});

/**
 * Cloud Function for sending an email notification when the data is updated.
 */
exports.alexSendEmailNotification = functions.firestore.document('/test_sushytsky/first').onUpdate( async (change) => {

  // Get an email address from updated data
  let email = change.after.data().email;

  // Get the org_id parameter from updated data
  let orgId = change.after.data().org_id;

  const mailOptions = {
    from: '<noreply@firebase.com>',
    to: email,
    dsn: {
      id: 'Request_DSN_delivered_messages',
      return: 'headers',
      notify: 'success',
      recipient: gmailEmail
    }
  };

  //Building Email message.
  mailOptions.subject = 'Testing data updating';
  mailOptions.text = 'The data is updated:) Org ID: ' + orgId;

  // For checking email status
  let resEmail = '';

  try {
    resEmail = await mailTransport.sendMail(mailOptions);
    console.log(`New email notification sent to ${email}`);
  } catch(error) {
    console.error('There was an error while sending the email:', error);
  }

  functions.logger.info(`Send update notification to email: ${email}`, {structuredData: true});
  functions.logger.info(resEmail.accepted, {structuredData: true});

  return resEmail;
});

/**
 * HTTP Authenticated Cloud Function.
 */
exports.alexAuthenticate = functions.https.onRequest(async (req, res) => {
  functions.logger.info(req.body.name, {structuredData: true});

  let userName = escapeHtml(req.body.name);
  let login = escapeHtml(req.body.login);
  let password = escapeHtml(req.body.password);

  res.send(`Welcome ${userName}`);
});