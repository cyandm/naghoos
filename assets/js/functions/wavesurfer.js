import WaveSurfer from "wavesurfer.js";

export function initAudioPlayers() {
  // پیدا کردن تمام کانتینرهایی که کلاس waveform دارند
  const containers = document.querySelectorAll(
    ".waveform-container:not(.initialized)",
  );

  containers.forEach((container) => {
    const audioUrl = container.dataset.url; // آدرس فایل صوتی از دیتا-اتریبیوت
    const playerContainer = container.querySelector(".waveform-id");
    const playBtn = container.querySelector(".play-btn");
    const durationEl = container.querySelector(".duration");

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

    wavesurfer.load(audioUrl);

    // مدیریت دکمه Play/Pause
    playBtn.addEventListener("click", () => {
      wavesurfer.playPause();
      playBtn.classList.toggle("playing");
    });

    // نمایش زمان (تبدیل به فارسی)
    wavesurfer.on("ready", () => {
      const duration = wavesurfer.getDuration();
      durationEl.innerText = formatTimePersian(duration);
    });

    // علامت‌گذاری برای اینکه دوباره مقداردهی نشود
    container.classList.add("initialized");
  });
}

// تابع کمکی برای فرمت زمان و تبدیل به اعداد فارسی
function formatTimePersian(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  const time = `${mins.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
  return time.replace(/\d/g, (d) => "۰۱۲۳۴۵۶۷۸۹"[d]);
}
