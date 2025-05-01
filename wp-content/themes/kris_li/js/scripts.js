// Navbar toggle

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

  function setupSubMenuToggles() {
    const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
    if (window.innerWidth < 1024) {
      dropdownToggles.forEach(function (toggle) {
        toggle.classList.add("mobile");
      });
    }
    dropdownToggles.forEach(function (toggle) {
      toggle.addEventListener("click", function (e) {
        e.preventDefault();
        const parent = this.parentNode;

        const subMenu = parent.querySelector(".sub-menu");

        if (subMenu) {
          subMenu.classList.toggle("is-active");
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
// DROPDOWN ICON  HANDLING
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

// handle the scrolling navigation bar
window.onscroll = function () {
  myFunction();
};

function myFunction() {
  const mainNavHeader = document.querySelector(".main-header");
  const mobileNavHeader = document.querySelector(".mobile-nav");
  const dropdownToggleIconBtn = document.querySelector(".dropdown-toggle");
  if (
    document.body.scrollTop > 250 ||
    document.documentElement.scrollTop > 150
  ) {
    mainNavHeader.classList.add("sticking");
    mobileNavHeader.classList.add("sticking");
    dropdownToggleIconBtn.classList.add("darkbtn");
  } else {
    mainNavHeader.classList.remove("sticking");
    dropdownToggleIconBtn.classList.remove("darkbtn");
    mobileNavHeader.classList.remove("sticking");
  }
}

// Dynamic logo presentation
document.addEventListener("DOMContentLoaded", function () {
  const mainHeader = document.querySelector(".main-header");
  const mobileNav = document.querySelector(".mobile-nav");

  function handleScroll() {
    const scrollPosition = window.scrollY;
    const threshold = 50;

    if (scrollPosition > threshold) {
      mainHeader.classList.add("sticking");
      mobileNav.classList.add("sticking");
    } else {
      mainHeader.classList.remove("sticking");
      mobileNav.classList.remove("sticking");
    }
  }

  // Run on scroll
  window.addEventListener("scroll", handleScroll);

  // Run on page load
  handleScroll();
});

//logos carousel scroller Section
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

// FAQ PAGE
const faqQuestion = document.querySelectorAll(".list-item_content--heading");

faqQuestion.forEach((question) => {
  question.addEventListener("click", () => {
    const answer = question.parentElement.nextElementSibling;
    answer.classList.toggle("faq-show");
    question.classList.toggle("rotate");
  });
});

// Contact Us Form
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contact-form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Clear previous errors
    document
      .querySelectorAll(".form-group, .checkbox-container, .captcha-container")
      .forEach((el) => el.classList.remove("error"));
    document
      .querySelectorAll(".error-message")
      .forEach((el) => (el.textContent = ""));
    const messages = document.getElementById("form-messages");
    messages.classList.remove("success", "error");
    messages.textContent = "";

    // Frontend validation
    const errors = {};
    const firstName = document.getElementById("first-name").value.trim();
    const lastName = document.getElementById("last-name").value.trim();
    const email = document.getElementById("email").value.trim();
    const contactNumber = document
      .getElementById("contact-number")
      .value.trim();
    const enquiryType = document.getElementById("enquiry-type").value.trim();
    const enquiry = document.getElementById("enquiry").value.trim();
    const terms = document.getElementById("terms").checked;
    const captcha = document.getElementById("captcha").checked;

    if (!firstName) {
      errors["first-name"] = "First name is required";
    } else if (firstName.length > 50) {
      errors["first-name"] = "First name must be less than 50 characters";
    }

    if (!lastName) {
      errors["last-name"] = "Last name is required";
    } else if (lastName.length > 50) {
      errors["last-name"] = "Last name must be less than 50 characters";
    }

    if (!email) {
      errors.email = "Email is required";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errors.email = "Invalid email format";
    }

    if (!contactNumber) {
      errors["contact-number"] = "Contact number is required";
    } else if (!/^[\d\s+()-]{7,20}$/.test(contactNumber)) {
      errors["contact-number"] = "Invalid phone number format";
    }

    if (!enquiryType) {
      errors["enquiry-type"] = "Enquiry type is required";
    } else if (enquiryType.length > 100) {
      errors["enquiry-type"] = "Enquiry type must be less than 100 characters";
    }

    if (!enquiry) {
      errors.enquiry = "Enquiry is required";
    } else if (enquiry.length > 1000) {
      errors.enquiry = "Enquiry must be less than 1000 characters";
    }

    if (!terms) {
      errors.terms = "You must agree to the Terms of Use and Privacy Policy";
    }

    if (!captcha) {
      errors.captcha = "Please verify you are not a robot";
    }

    // Display frontend validation errors
    if (Object.keys(errors).length > 0) {
      for (const field in errors) {
        if (field === "terms") {
          const container = document
            .getElementById("terms")
            .closest(".checkbox-container");
          container.classList.add("error");
          container.querySelector(".error-message").textContent = errors[field];
        } else if (field === "captcha") {
          const container = document
            .getElementById("captcha")
            .closest(".captcha-container");
          container.classList.add("error");
          container.querySelector(".error-message").textContent = errors[field];
        } else {
          const container = document
            .getElementById(field)
            .closest(".form-group");
          container.classList.add("error");
          container.querySelector(".error-message").textContent = errors[field];
        }
      }
      showToast("Please correct the errors in the form.", "error");
      return;
    }

    // Submit form via AJAX
    const formData = new FormData(form);
    fetch(contactFormAjax.ajaxurl, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showToast(
            data.message || "Your enquiry has been submitted successfully!",
            "success"
          );
          form.reset();
        } else {
          if (data.errors) {
            for (const field in data.errors) {
              if (field === "general") {
                showToast(data.errors[field], "error");
              } else if (field === "terms") {
                const container = document
                  .getElementById("terms")
                  .closest(".checkbox-container");
                container.classList.add("error");
                container.querySelector(".error-message").textContent =
                  data.errors[field];
              } else if (field === "captcha") {
                const container = document
                  .getElementById("captcha")
                  .closest(".captcha-container");
                container.classList.add("error");
                container.querySelector(".error-message").textContent =
                  data.errors[field];
              } else {
                const container = document
                  .getElementById(field)
                  .closest(".form-group");
                container.classList.add("error");
                container.querySelector(".error-message").textContent =
                  data.errors[field];
              }
            }
            if (!data.errors.general) {
              showToast("Please correct the errors in the form.", "error");
            }
          } else {
            showToast("An error occurred. Please try again.", "error");
          }
        }
      })
      .catch(() => {
        showToast("An error occurred. Please try again.", "error");
      });
  });
});

// Contact Message in toast
const timeout = 5000;

function showToast(message, type = "success") {
  const toastContainer = document.querySelector(".toast-container");

  const toast = document.createElement("div");
  toast.classList.add("toast", type);

  toast.innerHTML = `
      <div class="toast-content">
          <i class="bi icon bi-${getIcon(type)}"></i>
          <div class="message">
              <span class="text text-1">${capitalize(type)}</span>
              <span class="text text-2">${message}</span>
          </div>
      </div>
      <i class="bi bi-x-lg close"></i>
      <div class="progress active"></div>
  `;

  toastContainer.appendChild(toast);
  let showToast = setTimeout(() => {
    void toast.offsetHeight;
    toast.classList.add("active");
  }, 1);

  const progress = toast.querySelector(".progress");
  const closeIcon = toast.querySelector(".close");

  // Auto-remove toast after 5s
  const timer1 = setTimeout(() => {
    toast.classList.remove("active");
  }, timeout);

  const timer2 = setTimeout(() => {
    progress.classList.remove("active");
    setTimeout(() => toast.remove(), 400);
  }, timeout + 300);

  // Manual close
  closeIcon.addEventListener("click", () => {
    toast.classList.remove("active");
    clearTimeout(timer1);
    clearTimeout(timer2);
    clearTimeout(showToast);
    setTimeout(() => toast.remove(), 400);
  });
}

function getIcon(type) {
  switch (type) {
    case "success":
      return "check-circle-fill";
    case "error":
      return "x-circle-fill";
    case "warning":
      return "exclamation-triangle-fill";
    case "info":
      return "info-circle-fill";
    default:
      return "check-circle-fill";
  }
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
