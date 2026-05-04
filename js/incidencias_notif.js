let sonidoIncidencia = new Audio("../assets/sonido.mp3"); // tu sonido

setInterval(() => {
    fetch("incidencias_count.php")
        .then(res => res.json())
        .then(data => {
            if (data.nuevas > 0) {
                sonidoIncidencia.play();
                document.getElementById("badge_incidencias").innerText = data.nuevas;
            } else {
                document.getElementById("badge_incidencias").innerText = "";
            }
        });
}, 5000); // cada 5 segundos
//🔹 Asegúrate de tener un <span id="badge_incidencias"></span> en tu navbar o donde quieras mostrar el contador.