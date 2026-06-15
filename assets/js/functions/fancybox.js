import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import {
  resetVideoCarouselSwiper,
  shouldResetVideoCarouselSwiper,
} from "../functions/aboutUsVideos";

const FANCYBOX_SWIPER_MAP = {
  aboutUsVideos: "#aboutUsVideosSwiper",
  "shop-collection-videos": "#shopCollectionVideosSwiper",
};

export default function fancybox() {
  Fancybox.bind("[data-fancybox]", {
    on: {
      close: (fancyboxInstance) => {
        const slide = fancyboxInstance.getSlide?.();
        const trigger = slide?.triggerEl ?? slide?.$trigger;
        const group = trigger?.getAttribute?.("data-fancybox");

        if (!shouldResetVideoCarouselSwiper(group)) return;

        const swiperSelector = FANCYBOX_SWIPER_MAP[group];
        requestAnimationFrame(() => resetVideoCarouselSwiper(swiperSelector));
      },
    },
  });
}
