<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Twilio\Rest\Client;

class TwilioHelper {

    public function sendNotification($status, $motif = null) {
*heka hwoa
        // 🚨 Remove emojis (for now)
        $message = ($status === 'accepte')
            ? "Votre demande de financement a ete ACCEPTEE."
            : "Votre demande de financement a ete REFUSEE." .
            ($motif ? " Motif: " . $motif : "");

        try {
            $client = new Client($sid, $token);

            $result = $client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $message
                ]
            );

            error_log("✅ Twilio SMS Sent! SID: " . $result->sid);
            return true;

        } catch (\Twilio\Exceptions\TwilioException $e) {
            error_log("❌ Twilio Error: " . $e->getMessage());
            return false;

        } catch (\Exception $e) {
            error_log("❌ General Error: " . $e->getMessage());
            return false;
        }
    }
}