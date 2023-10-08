<template>
  <div id="videoPlayer">
    <div>
      <video autoplay="autoplay"
             id="video"
             v-bind:src="'/videos/' + videoFile + '.mp4'" v-on:ended="getVariants"
      ></video>
      <slider animation="fade" v-bind:autoplay=false v-if="variants.length !== 0 && videoFinished">
        <slider-item v-for="variant in variants"
                     :key="variant.name"
        >
          <p style="font-size: 20px; text-align: center;"
             :data-filename="variant.file"
             :data-name="variant.name"
             class="slider-text"
             v-on:click="choice"
          >{{ variant.name }}</p>
        </slider-item>
      </slider>
    </div>
    <div id="controls">
      <div class="video-track" style="display: none">
        <div class="timeline"></div>
      </div>
      <div class="buttons">
        <button class="play" v-on:click="play">Play</button>
        <button class="pause" v-on:click="pause">Pause</button>
      </div>
    </div>


  </div>
</template>

<script>
import {Slider, SliderItem} from "vue-easy-slider";

export default {
  props: ['videoFile', 'videoName'],
  name: 'player',
  mounted() {
    this.video = document.getElementById("video");
    fetch('/watch/' + this.videoName + '/variants', {
      method: 'POST'
    }).then((response) => {
      return response.json();
    })
        .then((data) => {
          this.variants = data.variants;
        });
  },
  data: function () {
    return {
      variants: [],
      video: null,
      videoFinished: false
    }
  },
  components: {Slider, SliderItem},
  methods: {
    play(e) {
      this.video.play();
    },
    pause(e) {
      this.video.pause();
    },
    rewind(e) {
      this.video.currentTime -= 5; // Уменьшаем время на пять секунд
    },
    forward(e) {
      this.video.currentTime += 5; // Увеличиваем время на пять секунд
    },
    choice(e) {
      this.videoFile = e.target.dataset.filename
      this.videoName = e.target.dataset.name;
      this.variants = []
      this.videoFinished = false;
      fetch('/watch/' + this.videoName + '/variants', {
        method: 'POST'
      }).then((response) => {
        return response.json();
      })
          .then((data) => {
            this.variants = data.variants;
          });
      this.video.play()
    },
    getVariants(e) {
      this.videoFinished = true
    }
  }
}
</script>