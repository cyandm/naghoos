/**
 * Main entry point for the theme's JavaScript.
 * you must add any functions for every javascript file to the import statement below.
 */

import { Modals } from "./functions/modals";
import { register } from "swiper/element/bundle";
import { Htmx } from "./functions/htmx";
import { VideoPlyr } from "./functions/plyr";
import videoCover from "./functions/videoCover";
import fancybox from "./functions/fancybox";
import { SubMenuMobile } from "./functions/subMenuMobile";
import { SubMenuDesktop } from "./functions/subMenuDesktop";
import { ShareBtn } from "./functions/shareBtn";
import { SearchPage } from "./functions/search";
import { Accordion } from "./functions/accordion";
import { BlogCategoryTabs } from "./functions/blogCategoryTabs";
import { BlogArchiveSort } from "./functions/blogArchiveSort";
import { InstockToggle } from "./functions/instockToggle";
import { initAudioPlayers } from "./functions/wavesurfer";
import { FaqTabs, FaqCard } from "./functions/faq";

Modals();
register();
Htmx();
VideoPlyr();
videoCover();
fancybox();
SubMenuMobile();
SubMenuDesktop();
ShareBtn();
SearchPage();
Accordion();
BlogCategoryTabs();
BlogArchiveSort();
InstockToggle();
initAudioPlayers();
FaqTabs();
FaqCard();
