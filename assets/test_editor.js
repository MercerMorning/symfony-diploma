import Vue from 'vue';

Vue.component('player', {
    props: ['videoFile', 'currentVideo'],
    data: function () {
        return {
            turn_edit: false,
            variants: [
                1
            ],
            cases: []
        }
    },
    methods: {
        send(e) {
            let formData = new FormData(document.getElementById("sendForm"));
            formData.set('current_video', this.currentVideo);
            formData.set('time', video.currentTime)
            let response = fetch('/video/edit', {
                method: 'POST',
                body: formData
            }).then((response) => {
                return response.json();
            })
                .then((data) => {
                    location.reload()
                    // this.videoFile = data.current_video_file;
                    // this.cases = data.current_cases
                });

        },
        switchEdit() {
            video.pause()
            this.turn_edit = !this.turn_edit
        }

    },
    template: `
      <div>
      <video id="video" v-bind:src="'/videos/' + videoFile + '.mp4'"></video>
      <div id="controls">
        <div class="video-track">
          <div class="timeline"></div>
        </div>
        <div class="buttons">
          <button class="play">Play</button>
          <button class="pause">Pause</button>
          <button class="rewind">&#60;Rewind</button>
          <button class="forward">Forward&#62;</button>
          <button class="edit" v-on:click="switchEdit">Edit&#62;</button>
        </div>
      </div>
      <div v-for="current_case in cases">
        <a v-bind:href="'/editor/' + current_case + '/edit'"
           v-bind:title="current_case">{{ current_case }}</a>
        <br>
      </div>
      <div id="variants-window" v-if="turn_edit">
        <form v-on:submit.prevent="send" id="sendForm">
          <div v-for="(value, name) in variants">
            <br>
            Вариант
            <input title="Вариант" v-bind:name="'video[' + name + '][variant]'" type="text">
            <br>
            Видео
            <input title="Видео" v-bind:name="'video[' + name + '][file]'" type="file">
            <br>
            Аудио
            <input title="Аудио" v-bind:name="'video[' + name + '][audio]'" type="file">
            <br>
            Громкость звука
            <input title="Громкость звука" v-bind:name="'video[' + name + '][loud]'" type="number">
          </div>
          <div id="add-variant" v-on:click="variants.push(1)">
            Добавить
          </div>
          <input type="submit">
        </form>
      </div>
      </div>
    `
})

const app = new Vue({
    el: '#videoPlayer',
})


window.app = app;

let video = document.getElementById("video");            // Получаем элемент video
let videoTrack = document.querySelector(".video-track"); // Получаем элемент Видеодорожки
let time = document.querySelector(".timeline");          // Получаем элемент времени видео
let btnPlay = document.querySelector(".play");           // Получаем кнопку проигрывания
let btnPause = document.querySelector(".pause");         // Получаем кнопку паузы
let btnRewind = document.querySelector(".rewind");       // Получаем кнопки перемотки назад
let btnForward = document.querySelector(".forward");

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
//
//     fetch('/video/edit', {
//         method: 'POST',
//         body: JSON.stringify({
//             current_video: localStorage.getItem('currentVideo'),
//             time: video.currentTime,
//             variant: prompt('Variant?')
//         })
//     })
// });
