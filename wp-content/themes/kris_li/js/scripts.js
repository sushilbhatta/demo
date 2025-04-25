// Navbar toggle and dropdown

const backdrop = document.querySelector(".backdrop");
const toggleButton = document.querySelector(".toggle-button");
const toggleButtonWhite = document.querySelector(".toggle-btn--white");
const mobileNav = document.querySelector(".mobile-nav__container");

backdrop.addEventListener("click", function () {
  backdrop.classList.remove("open");
  mobileNav.classList.remove("open");
});
if (toggleButton) {
  toggleButton.addEventListener("click", function () {
    backdrop.classList.toggle("open");
    mobileNav.classList.toggle("open");
  });
} else {
  toggleButtonWhite.addEventListener("click", function () {
    backdrop.classList.toggle("open");
    mobileNav.classList.toggle("open");
  });
}

// dropdown toggle
document.addEventListener("DOMContentLoaded", function () {
  setupSubMenuToggles();

  // Function to handle submenu toggles
  function setupSubMenuToggles() {
    const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
    console.log(dropdownToggles);

    dropdownToggles.forEach(function (toggle) {
      toggle.addEventListener("click", function (e) {
        e.preventDefault();

        // Find the parent list item
        const parent = this.parentNode;

        // Find the submenu (next element after the toggle button)
        const subMenu = parent.querySelector(".sub-menu");

        if (subMenu) {
          // Toggle submenu visibility
          subMenu.classList.toggle("is-active");

          // Toggle aria-expanded
          const isExpanded = this.getAttribute("aria-expanded") === "true";
          this.setAttribute("aria-expanded", !isExpanded);
        }
      });
    });
  }

  if (window.innerWidth > 768) {
    setupDesktopHoverEffect();
  }

  function setupDesktopHoverEffect() {
    const menuItemsWithChildren = document.querySelectorAll(
      ".top-menu .menu-item-has-children"
    );

    menuItemsWithChildren.forEach(function (item) {
      item.addEventListener("mouseenter", function () {
        const subMenu = this.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.classList.add("is-active");
        }
      });

      item.addEventListener("mouseleave", function () {
        const subMenu = this.querySelector(".sub-menu");
        if (subMenu) {
          subMenu.classList.remove("is-active");
        }
      });
    });
  }

  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      setupDesktopHoverEffect();
    }
  });
});

const DROPDOWN_ICONS = [
  `${myTheme.themeUrl}/images/dropdown_icon/medical_cleaning.svg`,
  `${myTheme.themeUrl}/images/dropdown_icon/office_cleaning.svg`,
  `${myTheme.themeUrl}/images/dropdown_icon/warehouse.svg`,
  `${myTheme.themeUrl}/images/dropdown_icon/warehouse.svg`,
  `${myTheme.themeUrl}/images/dropdown_icon/warehouse.svg`,
];

const subMenuItems = document.querySelectorAll(".top-menu .sub-menu li");
const subMenuMobileItems = document.querySelectorAll(
  ".mobile-menu .sub-menu li"
);

subMenuItems.forEach((item, index) => {
  const iconWrapper = document.createElement("div");
  iconWrapper.innerHTML = `<img src="${DROPDOWN_ICONS[index]}" alt="Icon">`;

  const anchor = item.querySelector(".top-menu .sub-menu a");
  const MobileAnchor = item.querySelector(".mobile-menu .sub-menu a");

  anchor.classList.add("service-icon-wrapper");
  if (anchor) {
    anchor.prepend(iconWrapper);
  }
});

subMenuMobileItems.forEach((item, index) => {
  const iconWrapper = document.createElement("div");
  iconWrapper.innerHTML = `<img src="${DROPDOWN_ICONS[index]}" alt="Icon">`;

  const anchor = item.querySelector(".mobile-menu .sub-menu a");

  anchor.classList.add("service-icon-wrapper");
  if (anchor) {
    anchor.prepend(iconWrapper);
  }
});

// Navbar  Observer
const primaryHeader = document.querySelector(".main-header");
const heroSection = document.querySelector("#carousel");

// Create the scroll watcher
const scrollWatcher = document.createElement("div");
scrollWatcher.setAttribute("data-scroll-watcher", "");
heroSection.before(scrollWatcher);

// Observer logic
const navObserver = new IntersectionObserver(
  (entries) => {
    primaryHeader.classList.toggle("sticking", !entries[0].isIntersecting);
  },
  { threshold: 1, rootMargin: "100px 0px 0px 0px" }
);

navObserver.observe(scrollWatcher);

// scroller
const scrollers = document.querySelectorAll(".scroller");
if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
  addAnimation();
}

function addAnimation() {
  scrollers.forEach((scroller) => {
    scroller.setAttribute("data-animated", true);

    const scrollerInner = scroller.querySelector(".scroller__inner");
    const scrollerContent = Array.from(scrollerInner.children);

    // duplicate the logos
    scrollerContent.forEach((item) => {
      const duplicatedItem = item.cloneNode(true);
      duplicatedItem.setAttribute("aria-hidden", true);
      scrollerInner.appendChild(duplicatedItem);
    });
  });
}
