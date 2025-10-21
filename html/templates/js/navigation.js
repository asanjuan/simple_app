// Identificador único de pestaña
    let tabId = sessionStorage.getItem("tab_id");
    if (!tabId) {
        tabId = Math.random().toString(36).substr(2, 9);
        sessionStorage.setItem("tab_id", tabId);
    }

    function getHistory() {
        return JSON.parse(sessionStorage.getItem("history_" + tabId)) || [];
    }

    function setHistory(history) {
        sessionStorage.setItem("history_" + tabId, JSON.stringify(history));
    }

    // Guardar en el historial si es diferente de la última entrada
    function savePage(url) {
        // No guardar si contiene "&new" o "?new"
        if (url.includes("&new") || url.includes("?new")) {
            //console.log("No se guarda en historial porque contiene &new:", url);
            return;
        }
        let history = getHistory();
        if (history.length === 0 || history[history.length - 1] !== url) {
            history.push(url);
            setHistory(history);
        }
    }

    // Volver atrás
    function goBack() {
        let history = getHistory();
        let currentUrl = window.location.pathname + window.location.search;

        if (history.length === 0) {
            //alert("No hay historial en esta pestaña");
            return;
        }

        if (history.length > 1) {
            if (history[history.length - 1] === currentUrl) 
                history.pop(); // quitar la actual
            let previous = history.pop(); // quitar y obtener la anterior
            setHistory(history);
            window.location.href = previous;
        } else {
            //alert("No hay página anterior en esta pestaña");
        }
    }

    // Guardar al cargar, evitando duplicados por recarga
    window.addEventListener("load", () => {
        savePage(window.location.pathname + window.location.search);
        //console.log("Historial pestaña:", getHistory());
    });