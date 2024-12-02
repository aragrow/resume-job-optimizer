<?php
if (!defined('ABSPATH')) exit;

function rjo_set_apiuri() {
    return get_option('rjo_ai_uri');
}

function rjo_set_apikey() {
    return get_option('rjo_ai_key');
}

function rjo_set_prompt() {
    return 
    "You are a skilled recruiter specializing in optimizing resumes and cover letters to seamlessly pass through Applicant Tracking Systems (ATS) 
    during the initial screening process.

Task:

Given a candidate's current resume and a specific job description, please:
    Optimize the Resume:
        Identify and incorporate relevant keywords from the job description.
        Tailor the resume's format and content to align with the ATS's requirements.
        Ensure the resume is clear, concise, and easy to read.
        Remove skills and experience that do not match with the job description.
        Include all education.
        Use the same formatting as the provided resume.
    Draft a Tailored Cover Letter:
        Highlight the candidate's most relevant skills and experiences.
        Quantify achievements and results to demonstrate impact.
        Customize the letter to address specific points in the job description.
        Maintain a professional and engaging tone.
    Format the Output:
        Do not write in a superfluous/colorful way.
        Present both the optimized resume and cover letter in HTML format.
        Consider using basic HTML tags (e.g., <h1>, <p>, <ul>) and CSS styles for formatting.
        Exclude de html header and body tags.";
}

function rjo_send_to_gemini_api ($resume_text, $job_description) {

    error_log('Exec-> rjo_send_to_gemini_api');
    
    $prompt=  rjo_set_prompt();

    // Set the API key from the environment variable
    $apiKey = getenv('GEMINI_API_KEY');
    if (!$apiKey) {
        $apiKey = rjo_set_apikey();
        if (!$apiKey) 
            die("API_KEY eis not set.\n");
    }

    $apiURI = rjo_set_apiuri()."?key=$apiKey";


    // Define the payload for the request
    $requestPayload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => "$prompt
                            Resume: $resume_text
                            Job Description: $job_description"
                    ]
                ]
            ]
        ]
    ];
      

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiURI); // Replace with the correct endpoint
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload));

    // Disable SSL verification (for local testing only)
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the request
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return;
    }

    $responseJson = json_decode($response, true);
   

    if (isset($responseJson['error'])) {
        error_log("API Error: " . $responseJson['error']['message']);
    } else {
        $item = $responseJson['candidates'][0]['content']['parts'][0]['text'];
        if (isset($item) ) {
            $anwser = $item;
        } else {
            $anwser = '';  // Handle case where resume is missing
        }
    }

    curl_close($ch);
    
    $anwser = [
        'status' => (isset($responseData['error']))?false:true,
        'anwser' => $anwser
    ];

    return $anwser;

}