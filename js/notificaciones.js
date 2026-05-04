let sonido = new Audio("../assets/sonido.mp3");

setInterval(() => {
    fetch("notificaciones_count.php")
        .then(r => r.json())
        .then(data => {
            if (data.nuevas > 0) {
                sonido.play();
                document.getElementById("badge").innerText = data.nuevas;
            }
        });
}, 5000);
