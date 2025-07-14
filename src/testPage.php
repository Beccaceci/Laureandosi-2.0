<?php    
    require_once __DIR__ . DIRECTORY_SEPARATOR . "laureandosi2" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR  . "GUI.php";

    const INDIRIZZO_TEST = "nome.cognome@studenti.unipi.it"; //INSERIRE QUI UN INDIRIZZO A CUI MANDARE I PROSPETTI
    function testCompleto() {
        // Imposto un gestore di errori personalizzato per catturare tutti i tipi di errori
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            // Non facciamo nulla, semplicemente catturiamo l'errore per evitare che venga visualizzato
            return true;
        });
        
        $dati = array(
            (object)array("matricola" => "123456", "cdl" => "t-inf", "data" => "2023-01-04"),
            (object)array("matricola" => "234567", "cdl" => "m-ele", "data" => "2023-01-04"),
            (object)array("matricola" => "345678", "cdl" => "t-inf", "data" => "2023-01-04"),
            (object)array("matricola" => "456789", "cdl" => "m-tel", "data" => "2023-01-04"),
            (object)array("matricola" => "567890", "cdl" => "m-cyb", "data" => "2023-01-04"),
            (object)array("matricola" => "678901", "cdl" => "t-inf", "data" => "2023-01-04"),
            (object)array("matricola" => "999999", "cdl" => "t-inf", "data" => "2023-01-04")
        );

        //generazione pdf
        echo "<h3>Generazione dei prospetti di laurea</h3>";
        echo "<div class='test-section'>";
        foreach ($dati as $d) {
            $input = json_encode(array("array_matricole" => array($d->matricola), "cdl" => $d->cdl, "data" => $d->data));
    
            try {
                $res = classes\GUI::creaProspetti($input, true);
                if (json_decode($res)->success)
                    echo "<p class='success-message'>Generato prospetto per " . $d->matricola . "</p>";
                else
                    echo "<p class='error-message'>ERRORE durante la generazione del prospetto per " . $d->matricola . "</p>";
            }
            catch (\Exception $e) {
                echo "<p class='error-message'>ERRORE durante la generazione del prospetto per " . $d->matricola . "</p>";
            }
        }
        echo "</div>";

        echo "<h3>Visualizzazione dei prospetti di laurea</h3>";
        echo "<div class='pdf-links-section'>";
        // Aggiungo i link per visualizzare i pdf generati e confrontarli con i pdf attesi
        foreach ($dati as $d) {
            $filename = "laureandosi2" . DIRECTORY_SEPARATOR . "test_generati" . DIRECTORY_SEPARATOR . $d->cdl . DIRECTORY_SEPARATOR . $d->data . DIRECTORY_SEPARATOR . $d->matricola . ".pdf";
            $url_filename = str_replace("\\", "/", $filename); // Assicura che funzioni su Windows
            $url_filename = str_replace("/", "/", $url_filename); // Non ha effetto su Linux ma mantiene la coerenza
            
            echo "<div class='pdf-link-row'>";
            echo '<a href="' . $url_filename . '" target="_blank" class="pdf-link">Visualizza prospetto generato per ' . $d->matricola . '</a>';
            echo '<span class="separator">|</span>';
            
            // Percorso per il file di output corretto
            $output_path = "laureandosi2" . DIRECTORY_SEPARATOR . "output_giusti" . DIRECTORY_SEPARATOR . $d->matricola . "_output.pdf";
            echo '<a href="' . $output_path . '" target="_blank" class="pdf-link">Visualizza prospetto corretto per ' . $d->matricola . '</a>';
            echo "</div>";
        }    
        echo "</div>";

        $dati = array(
            (object)array("matricola" => "345678", "cdl" => "t-inf", "data" => "2023-01-04"),
            (object)array("matricola" => "456789", "cdl" => "m-tel", "data" => "2023-01-04"),
            (object)array("matricola" => "567890", "cdl" => "m-cyb", "data" => "2023-01-04"),
            (object)array("matricola" => "678901", "cdl" => "t-inf", "data" => "2023-01-04"),
            (object)array("matricola" => "999999", "cdl" => "t-inf", "data" => "2023-01-04")
        );

        echo "<h3>Invio dei prospetti di laurea</h3>";
        echo "<div class='test-section'>";
        //invio dei pdf
        foreach ($dati as $d) {
            $input = json_encode(array("matricola" => $d->matricola, "cdl" => $d->cdl, "data" => $d->data));
    
            try {
                $res = classes\GUI::inviaProspetto($input, INDIRIZZO_TEST);
                if (json_decode($res)->success)
                    echo "<p class='success-message'>Inviato prospetto per " . $d->matricola . "</p>";
                else
                    echo "<p class='error-message'>ERRORE durante l'invio del prospetto a " . $d->matricola . "</p>";
            }
            catch (\Exception $e) {
                echo "<p class='error-message'>ERRORE durante l'invio del prospetto a " . $d->matricola . "</p>";
            }
            
            sleep(2); // Aggiungo un ritardo di 2 secondi tra un invio e l'altro
        }
        echo "</div>";
        
        // Ripristino il gestore di errori predefinito
        restore_error_handler();
    }
?>


<!DOCTYPE html>
<html lang="it-IT">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="test.css">
        <title>Gestione Prospetti di Laurea: Test</title>
    </head>
    <body>
        <div class="container">
            <h1>Gestione Prospetti di Laurea - Test Completo</h1>
            <?php
                testCompleto();
            ?>
            
            <a href="unitTest.php">Unit Test</a>
        </div>
    </body>
</html>
