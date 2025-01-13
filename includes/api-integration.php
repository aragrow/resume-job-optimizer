<?php

/**
 * 
 *  The Python script to integrateh with Google Gemini is in:
 *  Documents/python/virutal/running-parallel-chains-python/app.py
 */

if (!defined('ABSPATH')) exit;

function rjo_set_apiuri() {
    return get_option('rjo_ai_uri');
}

function rjo_set_apikey() {
    return get_option('rjo_ai_key');
}

function rjo_get_persona_prompt($persona) {

    $prompts = [];

    $prompts["CTO for a Company"] = "Hi, I’m a developer who thrives on creating innovative, 
        scalable solutions and working on challenging projects. 
        I specialize in web and application development and have experience leading teams and driving technical growth. 
        This job description is for a position where the CTO (Alex Rivera) is the recipient. 
        Knowing that a CTO is focused on aligning technology with business goals and building reliable systems, could you help me tailor my resume 
            and craft a cover letter that speaks directly to those priorities and shows how I can contribute to their vision? Please base the name 
            and title of the response on the information provided. 
        If that information is not provided, don’t include it—keep it neutral.";
    $prompts["Small Business Owner"] = "Hey there! I’m a developer with a knack for helping small businesses succeed through technology. 
        Whether it’s creating user-friendly websites or streamlining operations with custom applications, I love making life easier for business owners. 
        This job description comes from Maria López, a small business owner. 
        She’s probably looking for someone who understands the unique challenges of running a small business and can deliver real value. 
        Can you help me write a resume and cover letter that highlights my ability to support her goals and take her business to the next level? 
        Please base the name and title of the response on the information provided. 
        If that information is not provided, don’t include it—keep it neutral.";
    $prompts["Recruiter for a Technology Agency"] ="Hi, I’m a developer who loves building software and solving problems. 
        With a background in web and application development and a collaborative work style, I’m confident I can excel in any tech team. 
        This job description is from Jordan Smith, a recruiter for a tech agency. 
        Recruiters like Jordan are probably focused on finding candidates who make their clients happy and fulfill specific technical and team-fit requirements. 
        Could you help me create a resume and cover letter that checks those boxes and makes me stand out as an ideal candidate? Please base the 
            name and title of the response on the information provided. 
        If that information is not provided, don’t include it—keep it neutral.";
    $prompts["Hiring Manager for a Company"] = "Hi, I’m a developer with a track record of delivering impactful software solutions. 
        I’ve worked closely with cross-functional teams, met tight deadlines, and contributed to achieving company objectives. 
        This job description is for a role where Sophia Carter, a hiring manager, is the intended recipient. 
        She’s likely focused on finding someone who’s not only technically skilled but also a great fit for her team and the company culture. 
        Can you help me refine my resume and craft a cover letter that connects with her priorities and shows how I can add value to her team? Please 
            base the name and title of the response on the information provided. 
        If that information is not provided, don’t include it—keep it neutral.";
    $prompts["Person with a Website"] = "Hi, I’m a developer who specializes in creating and maintaining websites that make an impact. 
        Whether it’s improving user experience, boosting SEO, or ensuring smooth performance, I’ve got the expertise to help a website shine. 
        This job description looks like it’s from Ethan Taylor, a website owner. 
        They’re likely looking for someone who understands the value of great design and functionality and can take their site to the next level. 
        Could you help me craft a resume and cover letter that highlights how I can do just that? Please base the name and title of the response 
            on the information provided. 
        If that information is not provided, don’t include it—keep it neutral.";

    return $prompts[$persona];

}

function rjo_set_prompt($persona) {

    $persona_prompt = rjo_get_persona_prompt($persona);

    return 
    "
Persona:

{$persona_prompt}

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

function rjo_send_to_gemini_api_0 ($resume_text, $job_description, $persona) {

    error_log('Exec-> rjo_send_to_gemini_api');
    
    $prompt=  rjo_set_prompt($persona);

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

function rjo_send_to_gemini_api ($resume_text, $job_description, $persona) {

    error_log('Exec-> rjo_send_to_gemini_api');
    
    $prompt=  rjo_set_prompt($persona);

    // Set the API key from the environment variable
    $apiKey = getenv('GEMINI_API_KEY');
    if (!$apiKey) {
        $apiKey = rjo_set_apikey();
        if (!$apiKey) 
            die("API_KEY eis not set.\n");
    }

    $apiURI = "https://127.0.0.1:5600/api/v1/";

    // Define the payload for the request
    $requestPayload = [
        'resume' => $resume_text,
        'job' => $job_description
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
   
    error_log(print_r($responseJson,true));
    
    if (isset($responseJson['error'])) {
        error_log("API Error: " . $responseJson['error']['message']);
    } else {
        $item = $responseJson['html_content'];
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