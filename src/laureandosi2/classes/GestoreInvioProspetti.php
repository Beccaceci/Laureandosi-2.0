<?php

    namespace classes;

    require_once __DIR__ . DIRECTORY_SEPARATOR . "Laureando.php";
    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "PHPMailer" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "PHPMailer.php";
    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "PHPMailer" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Exception.php";
    require_once dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "PHPMailer" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "SMTP.php";

    use Exception;
    use PHPMailer\PHPMailer\PHPMailer;

    class GestoreInvioProspetti {
        private Laureando $laureando;
        private string $dataLaurea;

        public function __construct(string $matricola, string $cdl, string $dataLaurea) {
            if ($matricola == "") {
                throw new Exception("Matricola vuota.");
            }

            $this->laureando = Laureando::getInstance((int)$matricola, $cdl);
            $this->dataLaurea = $dataLaurea;
        }

        /**
         * Invia il prospetto all'indirizzo email del laureando
         * @param string|null $indirizzo_test
         * @return void
        */
        public function inviaProspetto(string $indirizzo_test = null) : void {
            $prospetto = ($indirizzo_test != null) ? (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "test_generati") : (dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "prospetti_generati");
            $prospetto .= DIRECTORY_SEPARATOR . $this->laureando->cdl->cdl_short . DIRECTORY_SEPARATOR . $this->dataLaurea . DIRECTORY_SEPARATOR . $this->laureando->matricola . ".pdf";
            
            if (file_exists($prospetto)) {
                $indirizzo = ($indirizzo_test != null) ? $indirizzo_test : $this->laureando->email;
                $mail = $this->creaMail($indirizzo, $prospetto);
                $mail->SMTPKeepAlive = true;
                if (!$mail->send()) {
                    throw new Exception("Errore durante l'invio del prospetto a $this->laureando->matricola: " . $mail->ErrorInfo);
                }
                $mail->smtpClose();
            }
            else {
                throw new Exception("Prospetto di " . $this->laureando->matricola . " non trovato.");
            }
        }

        /**
         * Crea un oggetto PHPMailer per l'invio della mail
         * @param string $indirizzo
         * @param string $pathProspetto
         * @return PHPMailer
        */
        private function creaMail(string $indirizzo, string $pathProspetto) : PHPMailer {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = "mixer.unipi.it";
            $mail->Port = 25;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = false;
            $mail->SMTPOptions = array(
                "ssl" => array(
                    "verify_peer" => true,
                    "verify_peer_name" => true,
                    "allow_self_signed" => false,
                    "cafile" => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "wp-includes" . DIRECTORY_SEPARATOR . "certificates" . DIRECTORY_SEPARATOR . "ca-bundle.crt"
                )
            );

            $mail->CharSet = "UTF-8";
            $mail->setLanguage("it", join(DIRECTORY_SEPARATOR, array(dirname(__DIR__, 1), "lib", "PHPMailer", "language")));
            $mail->setFrom("no-reply-laureandosi@ing.unipi.it", "Laureandosi");
            $mail->AddAddress($indirizzo);
            $mail->AddAttachment($pathProspetto, "prospetto.pdf");

            $mail->Subject = "Appello di laurea in " . $this->laureando->cdl->cdl_short . "- indicatori per voto di laurea";
            $mail->Body = $this->laureando->cdl->txt_email;

            return $mail;
        }
    }

?>