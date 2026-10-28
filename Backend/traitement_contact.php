<?php

/* TRAITEMENT DU FORMULAIRE DE CONTACT */

// CHARGEMENT DE PHPMAILER

require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// REPONSE AU FORMAT JSON

header("Content-Type: application/json; charset=UTF-8");

// VERIFIER LA METHODE UTILISEE

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "succes" => false,
        "message" => "Méthode non autorisée."
    ]);

    exit;

}

// RECUPERATION DES CHAMPS

$nom = trim($_POST["nom"] ?? "");
$email = trim($_POST["email"] ?? "");
$sujet = trim($_POST["sujet"] ?? "");
$message = trim($_POST["message"] ?? "");

// VERIFICATION DES CHAMPS

if ($nom === "" || $email === "" || $sujet === "" || $message === "") {

    http_response_code(400);

    echo json_encode([
        "succes" => false,
        "message" => "Veuillez remplir tous les champs."
    ]);

    exit;

}

// VERIFICATION DE L'ADRESSE EMAIL

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        "succes" => false,
        "message" => "Adresse email invalide."
    ]);

    exit;

}

// VERIFICATION DE LA LONGUEUR DES CHAMPS

if (
    strlen($nom) > 150 ||
    strlen($email) > 254 ||
    strlen($sujet) > 200 ||
    strlen($message) > 5000
) {

    http_response_code(400);

    echo json_encode([
        "succes" => false,
        "message" => "Un ou plusieurs champs sont trop longs."
    ]);

    exit;

}

// RECUPERATION DE LA CONFIGURATION GMAIL

$gmail = getenv("PORTFOLIO_GMAIL_USER");
$motDePasse = getenv("PORTFOLIO_GMAIL_PASSWORD");

// VERIFICATION DE LA CONFIGURATION

if (!$gmail || !$motDePasse) {

    error_log("Portfolio : configuration SMTP manquante.");

    http_response_code(500);

    echo json_encode([
        "succes" => false,
        "message" => "Le service de contact est indisponible."
    ]);

    exit;

}

// CREATION DU MAIL

$mail = new PHPMailer(true);

// ENVOI DU MESSAGE

try {

    // CONFIGURATION DU SERVEUR SMTP

    $mail->isSMTP();

    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;

    $mail->Username = $gmail;
    $mail->Password = $motDePasse;

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // ENCODAGE DES CARACTERES

    $mail->CharSet = "UTF-8";

    // CONFIGURATION DE L'EXPEDITEUR

    $mail->setFrom($gmail, "Portfolio Folio Danny");

    // ADRESSE DE RECEPTION

    $mail->addAddress($gmail, "Folio Danny");

    // PERMETTRE DE REPONDRE AU VISITEUR

    $mail->addReplyTo($email, $nom);

    // CONTENU DU MAIL

    $mail->isHTML(false);

    $mail->Subject = "Portfolio - " . $sujet;

    $mail->Body =
        "NOUVEAU MESSAGE DEPUIS LE PORTFOLIO\n\n" .
        "Nom : " . $nom . "\n" .
        "Email : " . $email . "\n" .
        "Sujet : " . $sujet . "\n\n" .
        "MESSAGE :\n" .
        $message;

    // ENVOI DU MAIL

    $mail->send();

    // CONFIRMATION DE L'ENVOI

    echo json_encode([
        "succes" => true,
        "message" => "Votre message a bien été envoyé !"
    ]);

} catch (Exception $erreur) {

    // ENREGISTREMENT DE L'ERREUR

    error_log("Erreur PHPMailer : " . $mail->ErrorInfo);

    // MESSAGE D'ERREUR

    http_response_code(500);

    echo json_encode([
        "succes" => false,
        "message" => "Impossible d'envoyer le message. Veuillez réessayer."
    ]);

}

?>