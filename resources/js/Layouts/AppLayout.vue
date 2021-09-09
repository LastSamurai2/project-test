<template>
    <div id="app-container" :class="getMenuType">
        <topnav />
        <sidebar />
        <main>
            <div class="container-fluid">
              <flash-messages />
              <div>
                <slot name="header"></slot>
                <slot name="buttons"></slot>
              </div>
              <slot></slot>
            </div>
        </main>
        <Footer />
      <!-- Modal Portal -->
      <portal-target name="modal" multiple>
      </portal-target>
    </div>
</template>

<script>
import Sidebar from "../Piaf/containers/navs/Sidebar";
import Topnav from "../Piaf/containers/navs/Topnav";
import Footer from "../Piaf/containers/navs/Footer";
import FlashMessages from "../components/FlashMessages";
import { mapGetters } from "vuex";

export default {
    components: {
      topnav: Topnav,
      sidebar: Sidebar,
      FlashMessages,
      Footer
    },
    data() {
        return {
            show: false
        };
    },
    computed: {
        ...mapGetters(["getMenuType"])
    },
    mounted() {
        setTimeout(() => {
            document.body.classList.add("default-transition");
        }, 100);
    }
};
</script>
