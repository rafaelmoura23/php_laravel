document.addEventListener('DOMContentLoaded', function () {
        var timerElement = document.getElementById('timer-{{ $consulta->id }}');
        var countDownDate = new Date('{{ $consulta->data }} {{ $consulta->horario }}').getTime();

        var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

            if (distance < 0) {
                clearInterval(x);
                timerElement.innerHTML = "EXPIRED";
            }
        }, 1000);

});