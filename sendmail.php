<?php
    //this script will validate form msgs agains google recaptcha and will send the email if valid.

    // validate against spammers
        //get recaptcha value generated randomly when filling the form
        $recaptchaResponse = $_POST["g-recaptcha-response"];
        //recaptcha key given at https://www.google.com/recaptcha/admin#site/ 
        $secretKey = "6LcyEBEUAAAAAPb8_r7jmCbP1RYB9xb4c_jR5g4V";
        $recaptchaURL= "https://www.google.com/recaptcha/api/siteverify";
        //call Google Recaptcha, pass the repsose and validate
        $validationResponse = file_get_contents($recaptchaURL . "?secret=" . $secretKey . "&response=" . $recaptchaResponse);
        //decode JSON
        $validationResponse = json_decode($validationResponse); 
        //you can access the object values by this format, e.g. $validationResponse->success        will return 1.

    // Only process POST requests where the recaptcha has been validated.
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $validationResponse->success) {


        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
				$name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["message"]);

        // Check that data was sent to the mailer.
        if ( empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Oops! There was a problem with your submission. Please complete the form and try again.";
            exit;
        }

        // Set the recipient email address.
        // FIXME: Update this to your desired email address.
        $recipient = "wmiezan@gmail.com,jorluiseptor@gmail.com";

        // Set the email subject.
        $subject = "MiezanTraining website message from $name";

        // Build the email content.
        $email_content = "Name: $name\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Message:\n$message\n";
        
        // Build the email headers.
        $headers = "From: MiezanTraining Website <noreply@miezantraining.com>\r\n";
		$headers .= "Reply-To: noreply@miezantraining.com\r\n";
		$headers .= "X-Mailer: PHP/".phpversion();

        // Send the email.
        if (mail($recipient, $subject, $email_content, $headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong and we couldn't send your message.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

?>
