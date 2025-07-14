window.addEventListener("DOMContentLoaded", () => {
    const input_matricole = document.getElementById("matricole");
    const input_cdl = document.getElementById("cdl");
    const input_data = document.getElementById("data");       
    const btn_genera = document.getElementById("genera");
    const btn_apri = document.getElementById("apri");
    const btn_invia = document.getElementById("invia");
    const txt_output = document.getElementById("output");
    const prspt_creati = document.getElementById("prospetti-creati");
    const calendar_icon = document.querySelector(".calendar-icon");

    ////////// FUNZIONI //////////

    btn_genera.addEventListener("click", genera);
    btn_apri.addEventListener("click", apri);
    btn_invia.addEventListener("click", invia);
    
    // Aggiunge l'event listener all'icona del calendario
    if (calendar_icon) {
        calendar_icon.addEventListener("click", () => {
            input_data.showPicker();
        });
    }
    
    aggiungiCdl();

    function disabilita_pulsanti() {
        btn_genera.disabled = true;
        btn_apri.disabled = true;
        btn_invia.disabled = true;
    }

    function abilita_pulsanti() {
        btn_genera.disabled = false;
        btn_apri.disabled = false;
        btn_invia.disabled = false;
    }
    
    function aggiungiCdl() {
        prspt_creati.innerHTML = "";
        fetch("config/corsi_di_laurea.json")
            .then(response => response.json())
            .then(data => {
                input_cdl.innerHTML = "";

                Object.keys(data).forEach(key => {
                    const cdl = data[key];
                    const option = document.createElement("option");
                    option.value = cdl["cdl-short"];
                    option.text = cdl["cdl"];
                    input_cdl.appendChild(option);
                });
                abilita_pulsanti();
            })
            .catch(error => console.error("Errore nella ricerca dei corsi di laurea", error));
    }
    
    async function genera() {
        disabilita_pulsanti();
        prspt_creati.innerText = "";
        txt_output.innerText = "";
    
        const matricole = input_matricole.value;
        const cdl = input_cdl.value;
        const data = input_data.value;
        const array_matricole = matricole.split(/\s+/);
    
        const richiesta = JSON.stringify({array_matricole, cdl, data});
    
        try {
            const res = await fetch("API.php?API=creaProspetti", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: richiesta
            });
    
            const json = await res.json();
    
            if (res.ok)
                prspt_creati.innerText = "Prospetti creati";
            else
                txt_output.innerText = json.error;
        }
        catch (error) {
            txt_output.innerText = "Errore nella creazione dei prospetti";
        }
    
        abilita_pulsanti();
    }
    
    async function apri() {
        txt_output.innerText = "";
        const cdl = input_cdl.value;
        const data = input_data.value;

        try {
            const res = await fetch(`API.php?API=accediProspetti&cdl=${cdl}&data=${data}`);
            if (!res.ok)
                throw res;
            
            const prospetto = await res.blob();
            window.open(window.URL.createObjectURL(prospetto), '_blank').focus();
        }
        catch (err) {
            txt_output.innerText = "Errore nell'apertura dei prospetti";
        }
    }
    
    async function invia() {
        txt_output.innerText = "";
        disabilita_pulsanti();
    
        const matricole = input_matricole.value;
        const cdl = input_cdl.value;
        const data = input_data.value;
        const array_matricole = (matricole.split(/\s+/)).filter(matricola => matricola !== "");
        if (!array_matricole.length) {
            abilita_pulsanti();
            return;
        }

        let prospetti_totali = array_matricole.length;
        let prospetti_inviati = 0;

        for (let i = 0; i < array_matricole.length; i++) {
            const matricola = array_matricole[i];
            const richiesta = JSON.stringify({ matricola: matricola, cdl, data });
        
            try {
                const res = await fetch("API.php?API=inviaProspetto", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: richiesta
                });
        
                if (!res.ok)
                    throw new Error(`Errore HTTP: ${res.status}`);

                const json = await res.json();
                if (json.error)
                    throw new Error(json.error);
        
                prospetti_inviati++;
                txt_output.innerText = `Prospetto numero ${i+1} inviato correttamente`;
            }
            catch (error) {
                txt_output.innerText = `Errore nell'invio del prospetto numero ${i+1}`;
            }

            await new Promise(resolve => setTimeout(resolve, 5000));
        }

        txt_output.innerText = `Prospetti inviati: ${prospetti_inviati} di ${prospetti_totali}`;
        abilita_pulsanti();
    }
});