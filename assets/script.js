let address = document.getElementById('address').getAttribute('data-payment-address');
let time = (document.getElementById('progress-remain').getAttribute('data-time-to-pay')) * 60;

new QRCode(document.getElementById("qrcode"), address);

let initialTime = time;
let timeLeft = initialTime;

let interval;
let progressBarTextElement = document.getElementById('progress-remain');
let progressBarElement = document.getElementById('progress');

function render() {
    let progressPercentage = (timeLeft / initialTime) * 100;
    let minute = Math.floor(timeLeft / 60);
    let second = timeLeft % 60;
    progressBarElement.style.width = progressPercentage + '%';
    progressBarTextElement.innerHTML = leadZero(minute) + ":" + leadZero(second) + ' min';
}

function tick() {
    timeLeft = timeLeft - 1;
    if(timeLeft <= 0) {
        clearInterval(interval);
    }

    render();
}

function startProgressBar() {
    interval = setInterval(tick, 1000);
    render();
}

function leadZero(n) {
    return (n < 10 ? '0' : '') + n;
}

startProgressBar();

function copyToClipboard(elementId) {
    var aux = document.createElement("input");
    aux.setAttribute("value", document.getElementById(elementId).innerHTML);
    document.body.appendChild(aux);
    aux.select();
    document.execCommand("copy");
    document.body.removeChild(aux);
}

document.getElementById('address').addEventListener('click', function (){
    copyToClipboard('address')
})