document.addEventListener("alpine:init", () => {
  Alpine.data("navbar", () => ({
    openNav: false,
    openSearch: false,
    openUserMenu: false,

    toggleSearch(e) {
      const search = document.getElementsByName("q");

      // If there is more then 1 search field on the page, then focus on the second.
      // We can assume that the first search field is the one in the navbar and the second is visible on the page.
      if (search.length > 1) {
        search[1].scrollIntoView();
        search[1].focus();
      } else {
        this.openSearch = !this.openSearch;
      }
    },

    toggleNav() {
      this.openNav = !this.openNav;

      if (this.openNav) {
        // Set the root element to fixed position when the offcanvas menu is opened.
        // This prevents the navbar from moving when it got position: absolute from the sticky scroling behavior.
        this.$root.style.position = "fixed";
      }
    },

    toggleUserMenu() {
      this.openUserMenu = !this.openUserMenu;
    },

    offcanvasTransition: {
      ["x-transition:enter"]: "navbar-menu-offcanvas-transition-enter",
      ["x-transition:enter-start"]: "navbar-menu-offcanvas-transition-enter-start",
      ["x-transition:enter-end"]: "navbar-menu-offcanvas-transition-enter-end",
      ["x-transition:leave"]: "navbar-menu-offcanvas-transition-leave",
      ["x-transition:leave-start"]: "navbar-menu-offcanvas-transition-leave-start",
      ["x-transition:leave-end"]: "navbar-menu-offcanvas-transition-leave-end",
    },
    offcanvasOverlayTransition: {
      ["x-transition:enter"]: "navbar-menu-offcanvas-overlay-transition-enter",
      ["x-transition:enter-start"]: "navbar-menu-offcanvas-overlay-transition-enter-start",
      ["x-transition:enter-end"]: "navbar-menu-offcanvas-overlay-transition-enter-end",
      ["x-transition:leave"]: "navbar-menu-offcanvas-overlay-transition-leave",
      ["x-transition:leave-start"]: "navbar-menu-offcanvas-overlay-transition-leave-start",
      ["x-transition:leave-end"]: "navbar-menu-offcanvas-overlay-transition-leave-end",
    },
  }));
});

const navbar = document.getElementById("navbar");
let prevScrollPos = window.scrollY;

window.onscroll = function () {
  const navbarHeight = navbar.offsetHeight;
  const currentScrollPos = window.scrollY;

  if (prevScrollPos > currentScrollPos) {
    // Scrolling up
    if (currentScrollPos > navbarHeight) {
      navbar.style.position = "fixed";
      navbar.style.transform = "translateY(0)";
      navbar.classList.add("navbar-floating");
    }

    if (currentScrollPos === 0) {
      navbar.style.position = "absolute";
      navbar.classList.remove("navbar-floating");
    }
  } else {
    // Scrolling down
    navbar.classList.remove("navbar-floating");

    if (currentScrollPos < navbarHeight) {
      navbar.style.position = "absolute";
    }

    if (currentScrollPos > navbarHeight) {
      navbar.style.transform = `translateY(-${navbarHeight}px)`;
    }
  }

  prevScrollPos = currentScrollPos;
};
