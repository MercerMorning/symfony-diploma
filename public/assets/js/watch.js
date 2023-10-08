let video = document.getElementById("video");            // Получаем элемент video
let videoTrack = document.querySelector(".video-track"); // Получаем элемент Видеодорожки
let time = document.querySelector(".timeline");          // Получаем элемент времени видео
let btnPlay = document.querySelector(".play");           // Получаем кнопку проигрывания
let btnPause = document.querySelector(".pause");         // Получаем кнопку паузы
let btnRewind = document.querySelector(".rewind");       // Получаем кнопки перемотки назад
let btnForward = document.querySelector(".forward");
let btnEdit = document.querySelector(".edit");

btnPlay.addEventListener("click", function () {
    video.play(); // Запуск проигрывания
    // Запуск интервала
    videoPlay = setInterval(function () {
        // Создаём переменную времени видел
        let videoTime = Math.round(video.currentTime)
        // Создаём переменную всего времени видео
        let videoLength = Math.round(video.duration)
        // Вычисляем длину дорожки
        time.style.width = (videoTime * 100) / videoLength + '%';
    }, 10)
});

btnPause.addEventListener("click", function () {
    video.pause(); // Останавливает воспроизведение
    clearInterval(videoPlay) // убирает работу интервала
});

// Нажимаем на кнопку перемотать назад
btnRewind.addEventListener("click", function () {
    video.currentTime -= 5; // Уменьшаем время на пять секунд
});

// Нажимаем на кнопку перемотать вперёд
btnForward.addEventListener("click", function () {
    video.currentTime += 5; // Увеличиваем время на пять секунд
});

videoTrack.addEventListener("click", function (e) {
    let posX = e.clientX - 8; // Вычисляем позицию нажатия
    let timePos = (posX * 100) / 800; // Вычисляем процент перемотки
    time.style.width = timePos + '%'; // Присваиваем процент перемотки
    video.currentTime = (timePos * Math.round(video.duration)) / 100 // Перематываем
});

// btnEdit.addEventListener("click", function () {
//     video.pause(); // Останавливает воспроизведение
//     clearInterval(videoPlay) // убирает работу интервала
// });
