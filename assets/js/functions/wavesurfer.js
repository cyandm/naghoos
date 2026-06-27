import WaveSurfer from "wavesurfer.js";

export function initAudioPlayers() {
  const containers = document.querySelectorAll(
    ".waveform-container:not(.initialized)",
  );

  containers.forEach((container) => {
    if (container.offsetParent === null) {
      return;
    }

    const audioUrl = container.dataset.url;
    const playerContainer = container.querySelector(".waveform-id");
    const playBtn = container.querySelector(".play-btn");
    const durationEl = container.querySelector(".duration");

    if (!audioUrl || !playerContainer || !playBtn || !durationEl) {
      return;
    }

    const wavesurfer = WaveSurfer.create({
      container: playerContainer,
      waveColor: "#e0e0e0",
      progressColor: "#a62d2d",
      barWidth: 3,
      barGap: 3,
      barRadius: 4,
      height: 40,
      cursorWidth: 0,
      normalize: true,
    });

    const syncPlayingState = () => {
      playBtn.classList.toggle("playing", wavesurfer.isPlaying());
    };

    wavesurfer.load(audioUrl);

    playBtn.addEventListener("click", () => {
      wavesurfer.playPause();
    });

    wavesurfer.on("ready", () => {
      durationEl.innerText = formatTimePersian(wavesurfer.getDuration());
    });

    wavesurfer.on("play", syncPlayingState);
    wavesurfer.on("pause", syncPlayingState);
    wavesurfer.on("finish", syncPlayingState);

    container.classList.add("initialized");
  });
}

function formatTimePersian(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  const time = `${mins.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
  return time.replace(/\d/g, (d) => "۰۱۲۳۴۵۶۷۸۹"[d]);
}
